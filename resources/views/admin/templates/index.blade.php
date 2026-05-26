<!DOCTYPE html>
<html>
<head>
    <title>Admin - Templates</title>
</head>
<body>
    <h1>Templates</h1>
    <a href="{{ route('admin.templates.create') }}">Create New</a>

    @if(session('success'))
        <div style="color: green; margin: 10px 0;">{{ session('success') }}</div>
    @endif

    <table border="1" cellpadding="5" cellspacing="0" style="margin-top: 10px; width: 100%;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Blade Path</th>
                <th>Category</th>
                <th>Badge</th>
                <th>Is Active</th>
                <th>Sort Order</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
            <tr>
                <td>{{ $template->name }}</td>
                <td>{{ $template->blade_path }}</td>
                <td>{{ $template->category }}</td>
                <td>{{ $template->badge }}</td>
                <td>{{ $template->is_active ? 'Yes' : 'No' }}</td>
                <td>{{ $template->sort_order }}</td>
                <td>
                    <a href="{{ route('admin.templates.edit', $template) }}">Edit</a> |
                    <a href="{{ route('admin.templates.preview', $template) }}" target="_blank">Preview</a> |
                    <form action="{{ route('admin.templates.toggle', $template) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit">Toggle Active</button>
                    </form> |
                    <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this template?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 10px;">
        {{ $templates->links() }}
    </div>
</body>
</html>
