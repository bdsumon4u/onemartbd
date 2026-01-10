<table>
    <thead>
    <tr>
        <th>ItemType(*)</th>
        <th>StoreName(*)</th>
        <th>MerchantOrderId</th>
        <th>RecipientName(*)</th>
        <th>RecipientPhone(*)</th>
        <th>RecipientCity(*)</th>
        <th>RecipientZone(*)</th>
        <th>RecipientArea</th>
        <th>RecipientAddress(*)</th>
        <th>AmountToCollect(*)</th>
        <th>ItemQuantity(*)</th>
        <th>ItemWeight(*)</th>
        <th>ItemDesc</th>
        <th>SpecialInstruction</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $item)
        <?php
        $item_description = null;
        foreach ($item->get_products as $get_product) {
            $item_description .= $get_product->get_product->name . "\r\n";
        }
        if ($item->courier_id) {
            $credential = DB::table('pathao_apis')->select('access_token')->where('id', 1)->first();
            //dd($credential);
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list';
            $curl = curl_init();
            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer ' . $credential->access_token,
            ];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d1 = curl_exec($curl);
            $d1 = json_decode($d1, true);
            //curl_close($curl);

            foreach ($d1['data']['data'] as $item1) {
                if ($item->courier_city_id == $item1['city_id']) {
                    $courier_city = $item1['city_name'];
                }
            }
            //dd($courier_city);
            //dd($credential);
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/cities/' . $item->courier_city_id . '/zone-list';
            //$curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d2 = curl_exec($curl);
            $d2 = json_decode($d2, true);
            curl_close($curl);

            $data2 = [];
            foreach ($d2['data']['data'] as $item2) {
                if ($item->courier_zone_id == $item2['zone_id']) {
                    $courier_zone = $item2['zone_name'];
                }
            }
        }else{
            $courier_city = null;
            $courier_zone = null;
        }
        ?>

        <tr>
            <td>parcel</td>
            <td>Dorkeri Ponno.com</td>
            <td>{{ $item->invoice_id??null }}</td>
            <td>{{ $item->customer_name ?? null }}</td>
            <td>{{ $item->customer_phone ?? null }}</td>
            <td>{{ $courier_city ?? null }}</td>
            <td>{{ $courier_zone ?? null }}</td>
            <td></td>
            <td>{{ $item->customer_address ?? null }}</td>
            <td>{{ $item->total ?? 0 }}</td>
            <td>{{ $item->get_products->sum('qty') ?? 1 }}</td>
            <td>0.5</td>
            <td>{{ $item_description ?? null }}</td>
            <td></td>
        </tr>
    @endforeach
    </tbody>
</table>
