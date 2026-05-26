@extends('layouts.user.app')

@section('title', 'Edit: ' . $cv->title . ' - Resumify')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4">Edit Resume</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Resume Details Form --}}
        <div class="card mb-4">
            <div class="card-header">Resume Details</div>
            <div class="card-body">
                <form action="{{ route('resumes.update', $cv) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title"
                               class="form-control"
                               value="{{ old('title', $cv->title) }}" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" name="is_public" id="is_public"
                               class="form-check-input" value="1"
                               {{ $cv->is_public ? 'checked' : '' }}>
                        <label for="is_public" class="form-check-label">Make Public</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>

        {{-- Sections --}}
        <h4 class="mb-3">Sections</h4>

        @foreach($cv->sections as $section)
        <div class="card mb-3">
            <div class="card-header">
                {{ $section->title }}
                <span class="badge bg-secondary">{{ $section->type }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('resumes.sections.update', [$cv, $section]) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Section Title</label>
                        <input type="text" name="title" class="form-control"
                               value="{{ old('title', $section->title) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content (JSON)</label>
                        <textarea name="content" class="form-control" rows="4"
                            placeholder='e.g. {"name": "John Doe", "email": "john@example.com"}'
                        >{{ old('content', $section->content ? json_encode($section->content, JSON_PRETTY_PRINT) : '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-sm btn-outline-primary">Update Section</button>
                </form>
            </div>
        </div>
        @endforeach

        <a href="{{ route('resumes.index') }}" class="btn btn-link">← Back to Resumes</a>
    </div>
</div>
@endsection
