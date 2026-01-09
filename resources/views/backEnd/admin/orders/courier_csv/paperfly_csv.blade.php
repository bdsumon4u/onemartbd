<table>
    <thead>
    <tr>
        <th>Merchant Code</th>
        <th>Merchant Order Reference</th>
        <th>Pick-up Merchant Name</th>
        <th>Pick-up Merchant Address</th>
        <th>District Name</th>
        <th>Thana Name</th>
        <th>Pick-up Merchant Phone</th>
        <th>Package Option</th>
        <th>Delivery Option</th>
        <th>Product Brief</th>
        <th>Package Price</th>
        <th>Customer Name</th>
        <th>Customer Address</th>
        <th>Customer District Name</th>
        <th>Customer Thana Name</th>
        <th>Customer Phone</th>
        <th>Actual Price</th>
        <th>Delivery Charge</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <?php
        $item_description = null;
        foreach ($item->get_products as $key => $get_product) {
            $item_description .= $get_product->get_product->name . "\r\n";
        }
        ?>
        <tr>
            <td></td>
            <td>{{ $item->invoice_id }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>standard</td>
            <td>regular</td>
            <td>{{ $item_description ?? null }}</td>
            <td></td>
            <td>{{ $item->customer_name }}</td>
            <td>{{ $item->customer_address }}</td>
            <td></td>
            <td></td>
            <td>{{ $item->customer_phone }}</td>
            <td>{{ $item->total }}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
