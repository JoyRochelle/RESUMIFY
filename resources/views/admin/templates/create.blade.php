<!DOCTYPE html>
<html>
<head>
    <title>Admin - Create Template</title>
</head>
<body>
    <h1>Create Template</h1>
    
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <p>
            <label>Name:</label><br>
            <input type="text" name="name" value="{{ old('name') }}" required>
        </p>

        <p>
            <label>Blade Path (e.g. templates.eleanor-vance):</label><br>
            <input type="text" name="blade_path" value="{{ old('blade_path') }}" required>
        </p>

        <p>
            <label>Category:</label><br>
            <select name="category" required>
                <option value="professional" {{ old('category') == 'professional' ? 'selected' : '' }}>Professional</option>
                <option value="creative" {{ old('category') == 'creative' ? 'selected' : '' }}>Creative</option>
                <option value="technology" {{ old('category') == 'technology' ? 'selected' : '' }}>Technology</option>
                <option value="managerial" {{ old('category') == 'managerial' ? 'selected' : '' }}>Managerial</option>
            </select>
        </p>

        <p>
            <label>Description:</label><br>
            <textarea name="description" rows="3" cols="40">{{ old('description') }}</textarea>
        </p>

        <p>
            <label>Badge:</label><br>
            <input type="text" name="badge" value="{{ old('badge') }}">
        </p>

        <p>
            <label>Badge Color:</label><br>
            <select name="badge_color">
                <option value="">None</option>
                <option value="blue" {{ old('badge_color') == 'blue' ? 'selected' : '' }}>Blue</option>
                <option value="secondary" {{ old('badge_color') == 'secondary' ? 'selected' : '' }}>Secondary</option>
                <option value="purple" {{ old('badge_color') == 'purple' ? 'selected' : '' }}>Purple</option>
                <option value="green" {{ old('badge_color') == 'green' ? 'selected' : '' }}>Green</option>
            </select>
        </p>

        <p>
            <label>Is Premium:</label>
            <input type="checkbox" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}>
        </p>

        <p>
            <label>Is Active:</label>
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
        </p>

        <p>
            <label>Sort Order:</label><br>
            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
        </p>

        <p>
            <label>Style Config (JSON):</label><br>
            <textarea name="style_config" rows="5" cols="40">{{ old('style_config') }}</textarea>
        </p>

        <p>
            <label>Thumbnail:</label><br>
            <input type="file" name="thumbnail" accept="image/*">
        </p>

        <button type="submit">Create Template</button>
    </form>
    
    <p><a href="{{ route('admin.templates.index') }}">Back to Index</a></p>
</body>
</html>
