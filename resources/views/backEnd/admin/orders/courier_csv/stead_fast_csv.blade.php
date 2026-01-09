<table>
    <thead>
    <tr>
        <th>Invoice</th>
        <th>Name</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Amount</th>
        <th>Note</th>
        <th>Contact Name</th>
        <th>Contact Phone</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <tr>
            <td>{{ $item->invoice_id }}</td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->customer_address }}</td>
            <td>{{ $item->customer_phone }}</td>
            <td>{{ $item->total }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
