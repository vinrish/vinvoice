<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorizeForUser($request->user('api'), 'view', Client::class);
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $columns = array(0 => 'name', 1 => 'code', 2 => 'phone', 3 => 'email');
        $param = array(0 => 'like', 1 => 'like', 2 => 'like', 3 => 'like');
        $data = array();
        $clients = Client::where('deleted_at', '=', null);

        //Multiple Filter
        $Filtred = $helpers->filter($clients, $columns, $param, $request)
            // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('code', 'LIKE', "%{$request->search}%")
                        ->orWhere('phone', 'LIKE', "%{$request->search}%")
                        ->orWhere('email', 'LIKE', "%{$request->search}%");
                });
            });
        $totalRows = $Filtred->count();
        if($perPage == "-1"){
            $perPage = $totalRows;
        }
        $clients = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($clients as $client) {
            $item['id'] = $client->id;
            $item['name'] = $client->name;
            $item['phone'] = $client->phone;
            $item['tax_number'] = $client->tax_number;
            $item['code'] = $client->code;
            $item['email'] = $client->email;
            $item['country'] = $client->country;
            $item['city'] = $client->city;
            $item['adresse'] = $client->adresse;
            $data[] = $item;
        }

        $company_info = Setting::where('deleted_at', '=', null)->first();

        return response()->json([
            'clients' => $data,
            'company_info' => $company_info,
            'totalRows' => $totalRows,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorizeForUser($request->user('api'), 'create', Client::class);

        $this->validate($request, [
                'name' => 'required',
            ]
        );

        Client::create([
            'name' => $request['name'],
            'code' => $this->getNumberOrder(),
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'tax_number' => $request['tax_number'],
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $this->authorizeForUser($request->user('api'), 'update', Client::class);

        $this->validate($request, [
                'name' => 'required',
            ]
        );

        Client::whereId($id)->update([
            'name' => $request['name'],
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'tax_number' => $request['tax_number'],
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($request, $id): \Illuminate\Http\JsonResponse
    {
        $this->authorizeForUser($request->user('api'), 'delete', Client::class);

        Client::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);
    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Client::class);
        $selectedIds = $request->selectedIds;

        foreach ($selectedIds as $Client_id) {
            Client::whereId($Client_id)->update([
                'deleted_at' => Carbon::now(),
            ]);
        }
        return response()->json(['success' => true]);
    }


    //------------- get Number Order Customer -------------\\

    public function getNumberOrder()
    {
        $last = DB::table('clients')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

    //------------- Get Clients Without Paginate -------------\\

    public function Get_Clients_Without_Paginate()
    {
        $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);
        return response()->json($clients);
    }

    // import clients
    public function import_clients(Request $request)
    {
        $file_upload = $request->file('clients');
        $ext = pathinfo($file_upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($ext != 'csv') {
            return response()->json([
                'msg' => 'must be in csv format',
                'status' => false,
            ]);
        } else {
            $data = array();
            $rowcount = 0;
            if (($handle = fopen($file_upload, "r")) !== false) {
                $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
                $header = fgetcsv($handle, $max_line_length);
                $header_colcount = count($header);
                while (($row = fgetcsv($handle, $max_line_length)) !== false) {
                    $row_colcount = count($row);
                    if ($row_colcount == $header_colcount) {
                        $entry = array_combine($header, $row);
                        $data[] = $entry;
                    } else {
                        return null;
                    }
                    $rowcount++;
                }
                fclose($handle);
            } else {
                return null;
            }

            $rules = array('name' => 'required');

            //-- Create New Client
            foreach ($data as $key => $value) {
                $input['name'] = $value['name'];

                $validator = Validator::make($input, $rules);
                if (!$validator->fails()) {

                    Client::create([
                        'name' => $value['name'],
                        'code' => $this->getNumberOrder(),
                        'adresse' => $value['adresse'] == '' ? null : $value['adresse'],
                        'phone' => $value['phone'] == '' ? null : $value['phone'],
                        'email' => $value['email'] == '' ? null : $value['email'],
                        'country' => $value['country'] == '' ? null : $value['country'],
                        'city' => $value['city'] == '' ? null : $value['city'],
                        'tax_number' => $value['tax_number'] == '' ? null : $value['tax_number'],
                    ]);

                }


            }

            return response()->json([
                'status' => true,
            ], 200);
        }

    }
}
