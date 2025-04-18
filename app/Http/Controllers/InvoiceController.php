<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Provider;
use App\Models\Role;
use App\Models\Setting;
use App\utils\helpers;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ArPHP\I18N\Arabic;
use Carbon\Carbon;

class InvoiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        // How many items do you want to display.
        $perPage = $request->limit;

        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $param = array(
            0 => 'like',
            1 => '=',
            2 => '=',
            3 => '=',
        );
        $columns = array(
            0 => 'ref',
            1 => 'client_id',
            2 => 'provider_id',
            3 => 'date',
        );
        $data = array();

        // Check If User Has Permission View  All Records
        $invoices = Invoice::with('details', 'provider', 'client', 'user')
//            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($view_records) {
                if (!$view_records) {
                    return $query->where('user_id', '=', Auth::user()->id);
                }
            });

        //Multiple Filter
        $Filtred = $helpers->filter($invoices, $columns, $param, $request)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', "%{$request->search}%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('client', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', "%{$request->search}%");
                            });
                        });
                });
            });

        $totalRows = $Filtred->count();
        if($perPage == "-1"){
            $perPage = $totalRows;
        }

        $invoices = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($invoices as $invoice) {

            $item['id'] = $invoice['id'];
            $item['date'] = $invoice['date'];
            $item['ref'] = $invoice['ref'];
            $item['created_by'] = $invoice['user']->username ?? '';
            $item['provider_id'] = $invoice['provider']->id;
            $item['provider_name'] = $invoice['provider']->name;
            $item['provider_email'] = $invoice['provider']->email;
            $item['provider_tele'] = $invoice['provider']->phone;
            $item['provider_code'] = $invoice['provider']->code;
            $item['provider_adr'] = $invoice['provider']->adresse;
            $item['avatar'] = $invoice['provider']->avatar;
            $item['client_id'] = $invoice['client']['id'];
            $item['client_name'] = $invoice['client']['name'];
            $item['client_email'] = $invoice['client']['email'];
            $item['client_phone'] = $invoice['client']['phone'];
            $item['client_code'] = $invoice['client']['code'];
            $item['client_adr'] = $invoice['client']['adresse'];
            $item['warranty'] = $invoice['warranty_period'] . '' . $invoice['warranty_type'];
            $item['grand_total'] = number_format($invoice['grand_total'], 2, '.', '');
            $item['paid_amount'] = number_format($invoice['paid_amount'], 2, '.', '');
            $item['due'] = number_format($item['grand_total'] - $item['paid_amount'], 2, '.', '');

            $data[] = $item;
        }

//        $suppliers = provider::where('deleted_at', '=', null)->get(['id', 'name']);
//        $customers = client::where('deleted_at', '=', null)->get(['id', 'name']);

        return response()->json([
            'totalRows' => $totalRows,
            'invoices' => $data,
//            'customers' => $customers,
//            'suppliers' => $suppliers,
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);
        $suppliers = Provider::where('deleted_at', '=', null)->get(['id', 'name']);

        return response()->json([
            'suppliers' => $suppliers,
            'clients' => $clients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'supplier_id' => 'required|integer|exists:providers,id',
//            'client_id' => 'required|integer|exists:clients,id',
//            'net_tax' => 'required|numeric',
//            'grand_total' => 'required|numeric',
            'details' => 'required|array',
//            'details.*.product_name' => 'required|string',
//            'details.*.description' => 'nullable|string',
//            'details.*.unit_cost' => 'required|numeric',
//            'details.*.quantity' => 'required|integer',
//            'details.*.subtotal' => 'required|numeric',
        ]);

        $warrantyExpiryDate = $this->calculateWarrantyExpiryDate($request->date, $request->warranty_period, $request->warranty_type);

        if ($request->client['is_walking'] === true) {
            $customer = new Client();
            $customer->name = $request->client['name'];
            $customer->code = $this->getClientNumberOrder();
            $customer->phone = $request->client['phone'];
            $customer->is_walking = $request->client['is_walking'];
            $customer->save();

            $invoice = new Invoice();
            $invoice->ref = $this->getNumberOrder();
            $invoice->date = $request->date;
            $invoice->provider_id = $request->supplier_id;
            $invoice->client_id = $customer->id;
            $invoice->net_tax = $request->TaxNet;
            $invoice->grand_total = $request->GrandTotal;
            $invoice->tax_rate = $request->tax_rate;
            $invoice->warranty_period = $request->warranty_period;
            $invoice->warranty_type = $request->warranty_type;
            $invoice->warranty_expiry_date = $warrantyExpiryDate;
            $invoice->created_by = Auth::user()->id;
            $invoice->save();

            // Create invoice details
            foreach ($request->details as $detail) {
                $invoiceDetail = new InvoiceDetail([
                    'product_name' => $detail['product_name'],
                    'serial' => $detail['serial'],
                    'description' => $detail['description'],
                    'unit_cost' => $detail['unit_cost'],
                    'quantity' => $detail['quantity'],
                    'subtotal' => $detail['subtotal'],
                ]);
                $invoice->details()->save($invoiceDetail);
            }

            return response()->json([
                'message' => 'Invoice successfully created!',
                'invoice' => $invoice
            ], 201);
        }

        // Create the invoice
        $invoice = new Invoice();
        $invoice->ref = $this->getNumberOrder();
        $invoice->date = $request->date;
        $invoice->provider_id = $request->supplier_id;
        $invoice->client_id = $request->client_id;
        $invoice->net_tax = $request->TaxNet;
        $invoice->grand_total = $request->GrandTotal;
        $invoice->tax_rate = $request->tax_rate;
        $invoice->created_by = Auth::user()->id;
        $invoice->save();

        // Create invoice details
        foreach ($request->details as $detail) {
            $invoiceDetail = new InvoiceDetail([
                'product_name' => $detail['product_name'],
                'serial' => $detail['serial'],
                'description' => $detail['description'],
                'unit_cost' => $detail['unit_cost'],
                'quantity' => $detail['quantity'],
                'subtotal' => $detail['subtotal'],
            ]);
            $invoice->details()->save($invoiceDetail);
        }

        return response()->json([
            'message' => 'Invoice successfully created!',
            'invoice' => $invoice
        ], 201);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    //------------- Reference Number Order Purchase -----------\\

//    public function getNumberOrder(): string
//    {
//
//        $last = DB::table('invoices')->latest('id')->first();
//
//        if ($last) {
//            $item = $last->ref;
//            $nwMsg = explode("_", $item);
//            $inMsg = $nwMsg[1] + 1;
//            $code = $nwMsg[0] . '_' . $inMsg;
//        } else {
//            $code = 'SL_1111';
//        }
//        return $code;
//    }

    public function getNumberOrder(): string
    {
        // Get the current year
        $currentYear = date('y'); // 'y' for two-digit year, 'Y' for four-digit year

        // Get the last invoice reference
        $lastInvoice = DB::table('invoices')->latest('id')->first();

        // Default starting sequence number
        $sequenceNumber = 10000;

        if ($lastInvoice) {
            $lastRef = $lastInvoice->ref;
            $parts = explode('-', $lastRef);
            if (count($parts) === 3 && is_numeric($parts[1])) {
                $sequenceNumber = intval($parts[1]) + 1;
            }
        }

        // Generate a random number for the third part
        $randomNumber = rand(10000, 99999);

        // Generate the new reference number
        $newRef = sprintf('%s-%05d-%05d', $currentYear, $sequenceNumber, $randomNumber);

        return $newRef;
    }

    public function getClientNumberOrder()
    {
        $last = DB::table('clients')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

    //---------------- Get Details Purchase -----------------\\

    public function show(Request $request, $id): \Illuminate\Http\JsonResponse
    {

//        $this->authorizeForUser($request->user('api'), 'view', Purchase::class);
        $role = Auth::user()->roles()->first();
        $view_records = Role::findOrFail($role->id)->inRole('record_view');
        $invoice = Invoice::with('details')
//            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $details = array();

        // Check If User Has Permission view All Records
        if (!$view_records) {
            // Check If User->id === purchase->id
            $this->authorizeForUser($request->user('api'), 'check_record', $invoice);
        }

        $purchase_data['ref'] = $invoice['ref'];
        $purchase_data['date'] = $invoice['date'];
        $purchase_data['tax_rate'] = $invoice['tax_rate'];
        $purchase_data['TaxNet'] = $invoice['net_tax'];
        $purchase_data['supplier_name'] = $invoice['provider']->name;
        $purchase_data['supplier_email'] = $invoice['provider']->email;
        $purchase_data['supplier_phone'] = $invoice['provider']->phone;
        $purchase_data['supplier_adr'] = $invoice['provider']->adresse;
        $purchase_data['supplier_tax'] = $invoice['provider']->tax_number;
        $purchase_data['avatar'] = $invoice['provider']->avatar;
        $purchase_data['customer_id'] = $invoice['client']['id'];
        $purchase_data['customer_name'] = $invoice['client']['name'];
        $purchase_data['customer_email'] = $invoice['client']['email'];
        $purchase_data['customer_phone'] = $invoice['client']['phone'];
        $purchase_data['customer_code'] = $invoice['client']['code'];
        $purchase_data['customer_adr'] = $invoice['client']['adresse'];
        $purchase_data['warranty'] = $invoice['warranty_period'] . ' ' . $invoice['warranty_type'];
        $purchase_data['warranty_expiry'] = $invoice['warranty_expiry_date'];
        $purchase_data['GrandTotal'] = number_format($invoice['grand_total'], 2, '.', '');
        $purchase_data['paid_amount'] = number_format($invoice['paid_amount'], 2, '.', '');
        $purchase_data['due'] = number_format($purchase_data['GrandTotal'] - $purchase_data['paid_amount'], 2, '.', '');

        foreach ($invoice['details'] as $detail) {

            $data['quantity'] = $detail->quantity;
            $data['total'] = $detail->subtotal;
            $data['cost'] = $detail->unit_cost;
            $data['description'] = $detail->description;
            $data['name'] = $detail->product_name;
            $data['serial'] = $detail->serial;

            $details[] = $data;
        }

        $company = Setting::where('deleted_at', '=', null)->first();

        return response()->json([
            'details' => $details,
            'invoice' => $purchase_data,
            'company' => $company,
        ]);
    }

    //-------------- purchase PDF -----------\\

    public function Invoice_pdf(Request $request, $id)
    {
        $details = array();
        $helpers = new helpers();
        $Purchase_data = Invoice::with('details')
//            ->where('deleted_at', '=', null)
            ->findOrFail($id);

        $purchase['supplier_name'] = $Purchase_data['provider']->name;
        $purchase['supplier_phone'] = $Purchase_data['provider']->phone;
        $purchase['supplier_adr'] = $Purchase_data['provider']->adresse;
        $purchase['supplier_email'] = $Purchase_data['provider']->email;
        $purchase['supplier_tax'] = $Purchase_data['provider']->tax_number;
        $purchase['avatar'] = $Purchase_data['provider']->avatar;
        $purchase['customer_name'] = $Purchase_data['client']->name;
        $purchase['customer_phone'] = $Purchase_data['client']->phone;
        $purchase['customer_adr'] = $Purchase_data['client']->adresse;
        $purchase['customer_email'] = $Purchase_data['client']->email;
        $purchase['customer_tax'] = $Purchase_data['client']->tax_number;
        $purchase['warranty'] = $Purchase_data['warranty_period'] . ' ' . $Purchase_data['warranty_type'];
        $purchase['warranty_expiry'] = $Purchase_data['warranty_expiry_date'];
        $purchase['TaxNet'] = number_format($Purchase_data['net_tax'], 2, '.', '');
        $purchase['Ref'] = $Purchase_data['ref'];
        $purchase['date'] = $Purchase_data['date'];
        $purchase['GrandTotal'] = number_format($Purchase_data['grand_total'], 2, '.', '');
//        $purchase['paid_amount'] = number_format($Purchase_data['paid_amount'], 2, '.', '');
//        $purchase['due'] = number_format($purchase['GrandTotal'] - $purchase['paid_amount'], 2, '.', '');

        $detail_id = 0;
        foreach ($Purchase_data['details'] as $detail) {
            $data['detail_id'] = $detail_id += 1;
            $data['quantity'] = number_format($detail->quantity, 2, '.', '');
            $data['total'] = number_format($detail->subtotal, 2, '.', '');
            $data['description'] = $detail->description;
            $data['name'] = $detail->product_name;
            $data['serial'] = $detail->serial;
            $data['cost'] = number_format($detail->unit_cost, 2, '.', '');

            $details[] = $data;
        }

        $settings = Setting::where('deleted_at', '=', null)->first();
        $symbol = $helpers->Get_Currency_Code();

        $Html = view('pdf.invoice_pdf', [
            'symbol' => $symbol,
            'setting' => $settings,
            'purchase' => $purchase,
            'details' => $details,
        ])->render();

        $arabic = new Arabic();
        $p = $arabic->arIdentify($Html);

        for ($i = count($p)-1; $i >= 0; $i-=2) {
            $utf8ar = $arabic->utf8Glyphs(substr($Html, $p[$i-1], $p[$i] - $p[$i-1]));
            $Html = substr_replace($Html, $utf8ar, $p[$i-1], $p[$i] - $p[$i-1]);
        }

        $pdf = PDF::loadHTML($Html);
        return $pdf->download('invoice.pdf');

    }

    protected function calculateWarrantyExpiryDate($date, $period, $type)
    {
        $date = Carbon::parse($date);

        switch ($type) {
            case 'day':
                return $date->addDays($period)->toDateString();
            case 'month':
                return $date->addMonths($period)->toDateString();
            case 'year':
                return $date->addYears($period)->toDateString();
            default:
                throw new \InvalidArgumentException("Invalid warranty type: $type");
        }
    }
}
