<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <title>Invoice _{{$purchase['Ref']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
   </head>

   <body>
      <header class="clearfix">
         <div id="logo">
         <img src="{{asset('/images/avatar/'.$purchase['avatar'])}}">
         </div>
         <div id="company">
            <div><strong> Date: </strong>{{$purchase['date']}}</div>
            <div><strong> Number: </strong> {{$purchase['Ref']}}</div>
         </div>
         <div id="Title-heading">
             Invoice  : {{$purchase['Ref']}}
         </div>
      </header>
      <main>
         <div id="details" class="clearfix">
            <div id="client">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Supplier Info</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div><strong>Full Name :</strong> {{$purchase['supplier_name']}}</div>
                           <div><strong>Phone :</strong> {{$purchase['supplier_phone']}}</div>
                           <div><strong>Address :</strong>   {{$purchase['supplier_adr']}}</div>
                           <div ><strong>Email :</strong>  {{$purchase['supplier_email']}}</div>
                           @if($purchase['supplier_tax'])<div><strong>Tax Number :</strong>  {{$purchase['supplier_tax']}}</div>@endif
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
            <div id="invoice">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Company Info</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div id="comp"><strong>Name :</strong>{{$purchase['customer_name']}}</div>
                            @if(!empty($purchase['customer_adr']))
                                <div><strong>Address :</strong>  {{$purchase['customer_adr']}}</div>
                            @endif
                           <div><strong>Phone :</strong>  {{$purchase['customer_phone']}}</div>
                            @if(!empty($purchase['customer_email']))
                                <div><strong>Email :</strong> {{ $purchase['customer_email'] }}</div>
                            @endif
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
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
{{--         <div id="signature">--}}
{{--            @if($setting['is_invoice_footer'] && $setting['invoice_footer'] !==null)--}}
{{--               <p>{{$setting['invoice_footer']}}</p>--}}
{{--            @endif--}}
{{--         </div>--}}
      </main>
   </body>
</html>
