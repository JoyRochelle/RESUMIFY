<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Http\Requests\StoreResumeRequest;
use App\Http\Requests\UpdateResumeRequest;
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $resumes = auth()->user()->cvs()->latest('updated_at')->get();

        return view('resumes.index', compact('resumes'));
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
        ]);

        return redirect()->route('resumes.edit', $cv)->with('success', 'Resume Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cv $cv)
    {
        //ownership check
        abort_unless($cv->user_id === auth()->id(), 403);

        $cv->load('sections', 'template');

        return view('resumes.show', compact('cv'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cv $cv)
    {
        abort_unless($cv->user_id === auth()->id(), 403);

        $cv->load('sections', 'template');

        $templates = CvTemplate::where('is_active', true)->get();

        return view('resumes.edit', compact('cv', 'templates'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResumeRequest $request, Cv $cv)
    {
        abort_unless($cv->user_id === auth()->id(), 403);
        // Detect AJAX auto-save (not our job to implement)
        if ($request->ajax()) {
            return response()->json([
                'message' => 'Auto-save not implemented yet.',
            ], 501);
        }
        // Regular form update
        $cv->update($request->validated());
        return redirect()->route('resumes.edit', $cv)
                         ->with('success', 'Resume updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cv $cv)
    {
        abort_unless($cv->user_id === auth()->id(), 403);
        $cv->delete();
        return redirect()->route('resumes.index')
                         ->with('success', 'Resume deleted successfully!');
    }

     /**
     * Duplicate a resume and all its sections.
     */
    public function duplicate(Cv $cv)
    {
        abort_unless($cv->user_id === auth()->id(), 403);
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
        return redirect()->route('resumes.edit', $newCv)
                         ->with('success', 'Resume duplicated successfully!');
    }

    /**
     * Update a single section's content.
     */
    public function updateSection(Request $request, Cv $cv, CvSection $section)
    {
        abort_unless($cv->user_id === auth()->id(), 403);
        abort_unless($section->cv_id === $cv->id, 404);

        $request->validate([
            'title' => ['sometimes', 'string', 'max:100'],
        ]);

        $data = ['title' => $request->input('title', $section->title)];

        // Decode the JSON content string from the textarea
        if ($request->filled('content')) {
            $decoded = json_decode($request->input('content'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['content' => 'Invalid JSON format.']);
            }
            $data['content'] = $decoded;
        } else {
            $data['content'] = null;
        }

        $section->update($data);

        return redirect()->route('resumes.edit', $cv)
                        ->with('success', 'Section updated!');
    }

}
