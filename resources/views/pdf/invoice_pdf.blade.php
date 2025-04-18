<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice _{{$purchase['Ref']}}</title>
    <link rel="stylesheet" href="{{asset('/css/invoice_style.css')}}" media="all" />
</head>

<body>
<header class="clearfix">
    <div id="Title-heading">
        {{$purchase['supplier_name']}}
    </div>
</header>
<main>
    <div id="details" class="clearfix">
        <div>
            <div><strong>Invoice to</strong></div>
            <div>{{$purchase['customer_name']}}</div>
                        @if(!empty($purchase['customer_adr']))
                            <div> {{$purchase['customer_adr']}}</div>
                        @endif
                        <div> {{$purchase['customer_phone']}}</div>
                        @if(!empty($purchase['customer_email']))
                            <div> {{ $purchase['customer_email'] }}</div>
                        @endif
        </div>
        
        <br>
        
        <div>
            <div><strong>Supplier</strong></div>
            <div>{{$purchase['supplier_phone']}}</div>
                        <div> {{$purchase['supplier_adr']}}</div>
                        <div> {{$purchase['supplier_email']}}</div>
                        @if($purchase['supplier_tax'])<div> {{$purchase['supplier_tax']}}</div>@endif
        </div>

    </div>
    
    <div id="teef">
        <div id="inv_det"><strong>INVOICE ID: {{ $purchase['Ref'] }}</strong></div>
        <div id="company"><strong> INVOICE Date: </strong>{{$purchase['date']}}</div>
    </div>
    <br>
    <hr>
    <div id="details_inv">
        <table class="table-sm">
            <thead>
            <tr>
                <th>PRODUCT</th>
                <th>UNIT COST</th>
                <th>QUANTITY</th>
                <th>TOTAL</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($details as $detail)
                <tr>
                    <td>
                        <span>{{ $detail['name']}}</span>
                        @if($detail['serial'] && $detail['serial'] !==null)
                            <p>IMEI/SN : {{$detail['serial']}}</p>
                        @endif
                    </td>
                    <td>{{$detail['cost']}} </td>
                    <td>{{$detail['quantity']}}</td>
                    <td>{{$detail['total']}} </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div id="total">
        <table>
            <tr>
                <td>Order Tax</td>
                <td>{{$purchase['TaxNet']}} </td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{$symbol}} {{$purchase['GrandTotal']}} </td>
            </tr>
        </table>
    </div>
    <br>
    <br>
    <br>
    <hr>
    <div>
        @if($purchase['warranty'] && $purchase['warranty_expiry'] !== null)
            <p><strong>Warranty</strong> : {{$purchase['warranty']}}</p>
            <p><strong>Warranty Expiry</strong> : {{$purchase['warranty_expiry']}}</p>
        @endif
    </div>
</main>
</body>
</html>
