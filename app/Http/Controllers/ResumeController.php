<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        return view('resumes.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResumeRequest $request)
    {
        $cv = auth()->user()->cvs()->create($request->validated());

        // auto create the 4 default sections
        $cv->sections()->createMany([
            ['type' => 'personal_info',   'title' => 'Personal Info',   'content' => null, 'order' => 1],
            ['type' => 'work_experience', 'title' => 'Work Experience', 'content' => null, 'order' => 2],
            ['type' => 'education',       'title' => 'Education',       'content' => null, 'order' => 3],
            ['type' => 'skills',          'title' => 'Skills',          'content' => null, 'order' => 4],
            ['type' => 'target_job',      'title' => 'Target Job',      'content' => null, 'order' => 5],
        ]);

        return redirect()->route('user.manuscript')->with('success', 'Resume Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv)
    {
        //ownership check
        Gate::authorize('view', $cv);

        $cv->load('sections', 'template');

        return view('resumes.show', compact('cv'));
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
        if ($request->ajax()) {
            $cv->update($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Resume updated successfully.',
            ]);
        }
        // Regular form update
        $cv->update($request->validated());
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
     * Duplicate a resume and all its sections.
     */
    public function duplicate(Cv $cv)
    {
        Gate::authorize('view', $cv);
        // Clone the resume
        $newCv = $cv->replicate();
        $newCv->title = $cv->title . ' (Copy)';
        $newCv->save();
        // Clone each section
        foreach ($cv->sections as $section) {
            $newSection = $section->replicate();
            $newSection->cv_id = $newCv->id;
            $newSection->save();
        }
        return redirect()->route('user.manuscript', ['cv_id' => $newCv->id])
                         ->with('success', 'Resume duplicated successfully!');
    }

    /**
     * Update a single section's content.
     */
    public function updateSection(UpdateSectionRequest $request, Cv $cv, CvSection $section)
    {
        Gate::authorize('update', $cv);
        abort_unless($section->cv_id === $cv->id, 404);

        $request->validate([
            'title' => ['sometimes', 'string', 'max:100'],
        ]);

        $data = [
            'title' => $request->input('title', $section->title),
            'last_saved_at' => now(),
        ];

        if ($request->has('content')) {
            $contentInput = $request->input('content');
            // Content is already validated & sanitized by UpdateSectionRequest
            if (is_string($contentInput) && !empty($contentInput)) {
                $decoded = json_decode($contentInput, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return $request->wantsJson()
                        ? response()->json(['success' => false, 'message' => 'Invalid JSON format.'], 422)
                        : back()->withErrors(['content' => 'Invalid JSON format.']);
                }
                $data['content'] = $decoded;
            } else {
                $data['content'] = $contentInput;
            }
        }

        $section->update($data);

        // Also update cvs.job_target if the section being saved is target_job
        if ($section->type === 'target_job' && isset($data['content']['job_title'])) {
            $cv->update(['job_target' => $data['content']['job_title']]);
        }

        $atsScore = \App\Services\AtsScoreService::calculate($cv);
        $cv->update(['ats_score' => $atsScore]);

        if ($request->ajax() || $request->wantsJson()) {
            $cv->refresh(); // ensure the latest data is loaded
            $html = $cv->template->renderHtml($cv);
            return response()->json([
                'success' => true, 
                'saved_at' => now()->format('H:i:s'),
                'ats_score' => $atsScore,
                'section' => $section,
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
