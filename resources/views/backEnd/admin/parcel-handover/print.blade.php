<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Parcel Handover</title>
    <style>
        @media print {
            body {
                zoom: 75%;
            }

            /* .pagebreak {
                clear: both;
                page-break-after: always;
            } */
        }



        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        tr {
            border-bottom: 1px solid #ddd;
            text-align: left;
            padding: 8px;
        }
        th {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

    </style>
</head>

<body>

    <table>
        <thead>
            <tr>
                <th>SL.</th>
                <th>Invoice ID</th>
                <th>Customer Info</th>
                <th style="text-align: center;">COD</th>
            </tr>
        </thead>
        <tbody>
            @php($i = 1)
            @foreach ($orders as $item)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>
                        {{ $item->invoice_no }}

                    </td>
                    <td>
                        <span> <strong>Name</strong>
                            {{ $item->customer_name }}</span> <br>
                        <span> <strong>Phone</strong>
                            {{ $item->customer_phone }}</span> <br>
                        <span> <strong>Address</strong>
                            {{ $item->customer_address }}</span>
                    </td>
                    <td style="text-align: center; font-weight: bold; font-size: 16px;">
                        {{ $item->total }}<br>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        window.onload = function() {
            window.print();
            window.close();
        }
    </script>
</body>

</html>
