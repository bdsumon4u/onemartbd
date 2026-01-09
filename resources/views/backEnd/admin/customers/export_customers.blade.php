<table>
    <thead>
    <tr>
        <th>Name</th>
        <th>Address</th>
        <th>Phone</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->address }}</td>
            <td>{{ $item->phone }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
