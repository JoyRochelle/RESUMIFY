@extends('layouts.user.app')

@section('title', 'My Resumes - Resumify')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Resumes</h2>
            <a href="{{ route('resumes.create') }}" class="btn btn-primary">+ New Resume</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($resumes->isEmpty())
            <p class="text-muted">You haven't created any resumes yet.</p>
        @else
            <div class="row">
                @foreach($resumes as $resume)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $resume->title }}</h5>
                            <p class="text-muted small">
                                Updated {{ $resume->updated_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="card-footer bg-transparent d-flex gap-2">
                            <a href="{{ route('resumes.show', $resume) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            <a href="{{ route('resumes.edit', $resume) }}" class="btn btn-sm btn-outline-primary">Edit</a>

                            <form action="{{ route('resumes.duplicate', $resume) }}" method="POST">
                                @csrf
                                <button class="btn btn-sm btn-outline-info">Duplicate</button>
                            </form>

                            <form action="{{ route('resumes.destroy', $resume) }}" method="POST"
                                  onsubmit="return confirm('Delete this resume?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
