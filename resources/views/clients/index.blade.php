<div>
    <a href="{{ route('dashboard') }}">Dashboard</a>

    @if(session('success'))
        <div style="padding: 10px; background-color: #d4edda; color: #155724; margin: 10px 0; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div style="padding: 10px; background-color: #fff3cd; color: #856404; margin: 10px 0; border-radius: 4px;">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div style="padding: 10px; background-color: #f8d7da; color: #721c24; margin: 10px 0; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

    @if(session('selected_client'))
        <div style="padding: 10px; background-color: #cfe2ff; color: #084298; margin: 10px 0; border-radius: 4px;">
            Currently Selected Client: <strong>{{ session('selected_client')->name }}</strong>
            <form action="{{ route('clients.clear') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" style="margin-left: 10px; cursor: pointer;">Clear Selection</button>
            </form>
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Industry</th>
                <th>Services</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr style="{{ session('selected_client_id') == $client->id ? 'background-color: #e7f3ff;' : '' }}">
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->industry }}</td>
                    <td>{{ $client->services_provided }}</td>
                    <td>
                        <form action="{{ route('clients.select', $client->id) }}" method="POST">
                            @csrf
                            <button type="submit" style="cursor: pointer; text-decoration: underline; background: none; border: none; color: #0066cc;">
                                Select Client
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
