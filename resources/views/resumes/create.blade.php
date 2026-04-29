@extends('layouts.app')

@section('title', 'Create Resume - Resumify')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4">Create New Resume</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('resumes.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Resume Title</label>
                <input type="text" name="title" id="title"
                       class="form-control" value="{{ old('title') }}"
                       placeholder="e.g. Senior Product Designer" required>
            </div>

            <div class="mb-4">
                <label for="template_id" class="form-label">Choose Template</label>
                <select name="template_id" id="template_id" class="form-select" required>
                    <option value="">-- Select Template --</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}"
                            {{ old('template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                            {{ $template->is_premium ? '(Premium)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Resume</button>
            <a href="{{ route('resumes.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
