@extends('layouts.app')

@section('title', $cv->title . ' - Resumify')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>{{ $cv->title }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('resumes.edit', $cv) }}" class="btn btn-outline-primary">Edit</a>
                <form action="{{ route('resumes.duplicate', $cv) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-info">Duplicate</button>
                </form>
                <form action="{{ route('resumes.destroy', $cv) }}" method="POST"
                      onsubmit="return confirm('Delete this resume?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>

        <p class="text-muted">
            Template: {{ $cv->template->name ?? 'N/A' }} |
            Public: {{ $cv->is_public ? 'Yes' : 'No' }} |
            Updated: {{ $cv->updated_at->diffForHumans() }}
        </p>

        @foreach($cv->sections as $section)
        <div class="card mb-3">
            <div class="card-header">
                <strong>{{ $section->title }}</strong>
                <span class="badge bg-secondary">{{ $section->type }}</span>
            </div>
            <div class="card-body">
                @if($section->content)
                    <pre class="mb-0">{{ json_encode($section->content, JSON_PRETTY_PRINT) }}</pre>
                @else
                    <p class="text-muted mb-0">No content yet.</p>
                @endif
            </div>
        </div>
        @endforeach

        <a href="{{ route('resumes.index') }}" class="btn btn-link">← Back to Resumes</a>
    </div>
</div>
@endsection
