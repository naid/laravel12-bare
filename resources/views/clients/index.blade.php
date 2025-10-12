<div>
    <a href="{{ route('dashboard') }}">Dashboard</a>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Industry</th>
                <th>Services</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->industry }}</td>
                    <td>{{ $client->services_provided }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
