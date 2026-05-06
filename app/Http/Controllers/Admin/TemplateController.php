<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CvTemplate;
use App\Models\Cv;
use App\Models\CvSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = CvTemplate::orderBy('sort_order')->paginate(10);
        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'blade_path'   => 'required|string|max:100',
            'category'     => 'required|in:professional,creative,technology,managerial',
            'description'  => 'nullable|string|max:500',
            'badge'        => 'nullable|string|max:30',
            'badge_color'  => 'nullable|in:blue,secondary,purple,green',
            'is_premium'   => 'boolean',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
            'style_config' => 'nullable|json',
            'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('templates', 'public');
            $validated['thumbnail_url'] = $path;
        }

        CvTemplate::create($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Template created successfully.');
    }

    public function edit(CvTemplate $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, CvTemplate $template)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'blade_path'   => 'required|string|max:100',
            'category'     => 'required|in:professional,creative,technology,managerial',
            'description'  => 'nullable|string|max:500',
            'badge'        => 'nullable|string|max:30',
            'badge_color'  => 'nullable|in:blue,secondary,purple,green',
            'is_premium'   => 'boolean',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
            'style_config' => 'nullable|json',
            'thumbnail'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($template->thumbnail_url) {
                Storage::disk('public')->delete($template->thumbnail_url);
            }
            $path = $request->file('thumbnail')->store('templates', 'public');
            $validated['thumbnail_url'] = $path;
        }

        $template->update($validated);

        return redirect()->route('admin.templates.index')->with('success', 'Template updated successfully.');
    }

    public function destroy(CvTemplate $template)
    {
        if ($template->thumbnail_url) {
            Storage::disk('public')->delete($template->thumbnail_url);
        }
        
        $template->delete();

        return redirect()->route('admin.templates.index')->with('success', 'Template deleted successfully.');
    }

    public function toggle(CvTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        
        return back()->with('success', 'Template status toggled.');
    }

    public function preview(CvTemplate $template)
    {
        // Create a fake CV with sample data for preview
        $cv = new Cv();
        $cv->setRelation('template', $template);
        $cv->setRelation('sections', collect([
            new CvSection(['type' => 'personal_info', 'content' => [
                'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+1234567890',
                'title' => 'Senior Software Engineer',
                'location' => 'San Francisco, CA',
                'summary' => 'Experienced software engineer with a passion for building scalable web applications.'
            ]]),
            new CvSection(['type' => 'work_experience', 'content' => [
                ['title' => 'Software Engineer', 'company' => 'TechCorp', 'start_date' => '2022', 'end_date' => 'Present', 'description' => "Developed and maintained web applications.\nCollaborated with cross-functional teams.\nImproved application performance by 20%."],
                ['title' => 'Junior Developer', 'company' => 'WebSolutions', 'start_date' => '2020', 'end_date' => '2022', 'description' => "Assisted in building client websites.\nWrote clean, maintainable code."]
            ]]),
            new CvSection(['type' => 'education', 'content' => [
                ['degree' => 'B.S. in Computer Science', 'school' => 'University of Technology', 'start_date' => '2016', 'end_date' => '2020', 'description' => 'Graduated with Honors.']
            ]]),
            new CvSection(['type' => 'skills', 'content' => [
                ['name' => 'PHP', 'level' => 'Advanced'],
                ['name' => 'Laravel', 'level' => 'Advanced'],
                ['name' => 'JavaScript', 'level' => 'Intermediate'],
                ['name' => 'Vue.js', 'level' => 'Intermediate'],
            ]]),
        ]));

        return view($template->blade_path, compact('cv'));
    }
}
