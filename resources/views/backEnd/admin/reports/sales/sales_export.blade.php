<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sales Report</title>
    <style>
        * {
            font-family: Arial;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000
        }

        table th, td {
            border: 1px solid #000;
            padding: 5px;
        }
    </style>
</head>
<body>

<div style="text-align: center;margin: 20px 0;">
    <img height="60px" src="{{$settings->get_logo?asset($settings->get_logo->file_url):""}}" alt="">
    <p>{{$settings->website_address}}</p>
</div>
<div style="text-align: center;margin-top: 30px;">
    <h2>Sales Report</h2>
    @if($cr)
        <p><b>Date:</b> {{$cr}} to {{$cr}}</p>
    @else
        <p><b>Date:</b> {{$s_date}} to {{$e_date}}</p>
    @endif
</div>
<div style="text-align: center">
    <table>
        <thead>
        <tr>
            <th>SL.</th>
            <th>Product Name</th>
            <th>Sales Value</th>
            <th>Discount</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @php($i=1)
        <?php
        $total_sales = 0;
        $total_discount = 0;
        ?>
        @foreach($result as $key => $item)
            <?php
            $total_sales += $item['sales'];
            $total_discount += $item['discount'];
            $total = $total_sales - $total_discount;
            ?>
            <tr>
                <td>{{$i++}}</td>
                <td style="text-align: left">{{$key}}</td>
                <td>{{number_format($item['sales'],2)}}</td>
                <td>{{number_format($item['discount'],2)}}</td>
                <td>{{number_format($item['sales'] - $item['discount'],2)}}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th colspan="2">Total</th>
            <th>{{number_format($total_sales,2)}}</th>
            <th>{{number_format($total_discount,2)}}</th>
            <th>{{number_format($total,2)}}</th>
        </tr>
        </tfoot>
    </table>
</div>

<div style="display: flex;justify-content: space-between;margin-top: 80px;">
    <p style="border-top: 1px solid #000;">Signature</p>
    <p style="border-top: 1px solid #000;">Signature</p>
</div>
</body>
<script>
    window.onload = function () {
        window.print();
        window.close();
    }
</script>
</html>
