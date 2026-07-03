<?php

namespace App\Http\Controllers\User\Resume;

use App\Actions\Resumes\DuplicateResumeAction;
use App\Actions\Resumes\UpdateResumeSectionAction;
use App\Exceptions\ResumeQuotaExceededException;
use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use InvalidArgumentException;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resumes = auth()->user()->cvs()->latest('updated_at')->get();

        return redirect()->route('dashboard');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $templates = CvTemplate::where('is_active', true)->get();

        return view('user.resume.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResumeRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        $template = CvTemplate::findOrFail($data['template_id']);

        if (!$user->canCreateResume()) {
            return redirect()->route('user.upgrade-quota')
                ->with('error', 'Basic accounts can create 1 resume. Upgrade to Premium for unlimited resumes.');
        }

        if ($template->is_premium && !$user->canUsePremiumFeature('premium_templates')) {
            return redirect()->route('user.upgrade-quota')
                ->with('error', 'Premium templates are locked on Basic. Upgrade to unlock this design.');
        }

        $cv = $user->cvs()->create($data);

        // auto create the 4 default sections
        $cv->sections()->createMany([
            ['type' => 'personal_info',   'title' => 'Personal Info',   'content' => null, 'order' => 1],
            ['type' => 'work_experience', 'title' => 'Work Experience', 'content' => null, 'order' => 2],
            ['type' => 'education',       'title' => 'Education',       'content' => null, 'order' => 3],
            ['type' => 'skills',          'title' => 'Skills',          'content' => null, 'order' => 4],
            ['type' => 'target_job',      'title' => 'Target Job',      'content' => null, 'order' => 5],
        ]);

        return redirect()->route('user.manuscript', ['cv_id' => $cv->id])->with('success', 'Resume Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv)
    {
        //ownership check
        Gate::authorize('view', $cv);

        $cv->load('sections', 'template');

        return view('user.resume.show', compact('cv'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cv $cv)
    {
        Gate::authorize('update', $cv);

        $cv->load('sections', 'template');

        $templates = CvTemplate::where('is_active', true)->get();

        return redirect()->route('user.manuscript', ['cv_id' => $cv->id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResumeRequest $request, Cv $cv)
    {
        Gate::authorize('update', $cv);
        $data = $request->validated();

        if (isset($data['template_id'])) {
            $template = CvTemplate::findOrFail($data['template_id']);

            if ($template->is_premium && !auth()->user()->canUsePremiumFeature('premium_templates')) {
                $payload = [
                    'success' => false,
                    'error' => 'premium_required',
                    'message' => 'Upgrade to Premium to unlock this template.',
                    'upgrade_url' => route('user.upgrade-quota'),
                ];

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json($payload, 402);
                }

                return redirect()->route('user.upgrade-quota')->with('error', $payload['message']);
            }
        }

        if ($request->ajax()) {
            $cv->update($data);
            return response()->json([
                'success' => true,
                'message' => 'Resume updated successfully.',
            ]);
        }
        // Regular form update
        $cv->update($data);
        return redirect()->route('user.manuscript', ['cv_id' => $cv->id])
                         ->with('success', 'Resume updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cv $cv)
    {
        Gate::authorize('delete', $cv);
        $cv->delete();
        return redirect()->route('dashboard')
                         ->with('success', 'Resume deleted successfully!');
    }

    /**
     * Update the resume's template.
     */
    public function updateTemplate(Request $request, Cv $cv)
    {
        Gate::authorize('update', $cv);
        
        $request->validate([
            'template_id' => 'required|exists:cv_templates,id',
        ]);

        $template = CvTemplate::findOrFail($request->template_id);

        if ($template->is_premium && !auth()->user()->canUsePremiumFeature('premium_templates')) {
            return response()->json([
                'success' => false,
                'error' => 'premium_required',
                'message' => 'Upgrade to Premium to unlock this template.',
                'upgrade_url' => route('user.upgrade-quota'),
            ], 402);
        }
        
        $cv->update(['template_id' => $request->template_id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully.'
        ]);
    }
     /**
     * Duplicate a resume and all its sections.
     */
    public function duplicate(Cv $cv, DuplicateResumeAction $duplicateResume)
    {
        Gate::authorize('view', $cv);

        try {
            $newCv = $duplicateResume->execute($cv);
        } catch (ResumeQuotaExceededException $e) {
            return redirect()->route('user.upgrade-quota')
                ->with('error', $e->getMessage());
        }

        return redirect()->route('user.manuscript', ['cv_id' => $newCv->id])
                         ->with('success', 'Resume duplicated successfully!');
    }

    /**
     * Update a single section's content.
     */
    public function updateSection(
        UpdateSectionRequest $request,
        Cv $cv,
        CvSection $section,
        UpdateResumeSectionAction $updateResumeSection
    )
    {
        Gate::authorize('update', $cv);
        abort_unless($section->cv_id === $cv->id, 404);

        $request->validate([
            'title' => ['sometimes', 'string', 'max:100'],
        ]);

        try {
            $result = $updateResumeSection->execute(
                $cv,
                $section,
                $request->input('title'),
                $request->input('content'),
                $request->has('content')
            );
        } catch (InvalidArgumentException) {
            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => 'Invalid JSON format.'], 422)
                : back()->withErrors(['content' => 'Invalid JSON format.']);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $cv->refresh(); // ensure the latest data is loaded
            $html = $cv->template->renderHtml($cv);
            return response()->json([
                'success' => true, 
                'saved_at' => now()->format('H:i:s'),
                'ats_score' => $result['ats_score'],
                'ats_matched' => $result['ats_matched'],
                'section' => $result['section'],
                'html' => $html
            ]);
        }

        return redirect()->route('user.manuscript', ['cv_id' => $cv->id])
                        ->with('success', 'Section updated!');
    }

    /**
     * Add a new section to the resume.
     */
    public function storeSection(Request $request, Cv $cv)
    {
        Gate::authorize('update', $cv);

        $request->validate([
            'type' => ['required', 'string', 'in:certifications,projects,languages'],
            'title' => ['required', 'string', 'max:100'],
        ]);

        $cv->sections()->create([
            'type' => $request->type,
            'title' => $request->title,
            'content' => [],
            'order' => $cv->sections()->max('order') + 1,
        ]);

        return back()->with('success', 'Section added successfully!');
    }

    /**
     * Delete an optional section from the resume.
     */
    public function destroySection(Request $request, Cv $cv, CvSection $section)
    {
        Gate::authorize('update', $cv);
        abort_unless($section->cv_id === $cv->id, 404);

        $section->delete();

        if ($request->ajax() || $request->wantsJson()) {
            $cv->refresh();
            return response()->json([
                'success' => true,
                'html' => $cv->template->renderHtml($cv)
            ]);
        }

        return back()->with('success', 'Section removed successfully!');
    }

}
