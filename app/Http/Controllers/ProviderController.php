<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Image;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->authorizeForUser($request->user('api'), 'view', Provider::class);

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

        $providers = Provider::where('deleted_at', '=', null);

        //Multiple Filter
        $Filtred = $helpers->filter($providers, $columns, $param, $request)
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
        $providers = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($providers as $provider) {
            $item['id'] = $provider->id;
            $item['name'] = $provider->name;
            $item['phone'] = $provider->phone;
            $item['tax_number'] = $provider->tax_number;
            $item['code'] = $provider->code;
            $item['email'] = $provider->email;
            $item['country'] = $provider->country;
            $item['city'] = $provider->city;
            $item['adresse'] = $provider->adresse;
            $data[] = $item;
        }

        $company_info = Setting::where('deleted_at', '=', null)->first();

        return response()->json([
            'providers' => $data,
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
        $this->authorizeForUser($request->user('api'), 'create', Provider::class);

        request()->validate([
            'name' => 'required',
        ]);

        if ($request->hasFile('avatar')) {

            $image = $request->file('avatar');
            $filename = rand(11111111, 99999999) . $image->getClientOriginalName();

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/images/avatar/' . $filename));

        } else {
            $filename = 'no_avatar.png';
        }

        Provider::create([
            'name' => $request['name'],
            'code' => $this->getNumberOrder(),
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'avatar'    => $filename,
            'tax_number' => $request['tax_number'],
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function show(Provider $provider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function edit(Provider $provider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $this->authorizeForUser($request->user('api'), 'update', Provider::class);

        \Log::info('Received data: ', $request->all());
        request()->validate([
            'name' => 'required',
        ]);

        $provider = Provider::findOrFail($id);
        $currentAvatar = $provider->avatar;

        if ($request->avatar != $currentAvatar) {

            $image = $request->file('avatar');
            $path = public_path() . '/images/avatar';
            $filename = rand(11111111, 99999999) . $image->getClientOriginalName();

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/images/avatar/' . $filename));

            $userPhoto = $path . '/' . $currentAvatar;
            if (file_exists($userPhoto)) {
                if ($provider->avatar != 'no_avatar.png') {
                    @unlink($userPhoto);
                }
            }
        } else {
            $filename = $currentAvatar;
        }

        $provider->update([
            'name' => $request['name'],
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'avatar' => $filename,
            'tax_number' => $request['tax_number'],
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Provider  $provider
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Provider::class);

        Provider::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);

    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'delete', Provider::class);

        $selectedIds = $request->selectedIds;
        foreach ($selectedIds as $Provider_id) {
            Provider::whereId($Provider_id)->update([
                'deleted_at' => Carbon::now(),
            ]);
        }
        return response()->json(['success' => true]);
    }


    //----------- get Number Order Of Suppliers-------\\

    public function getNumberOrder()
    {

        $last = DB::table('providers')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

    // import providers
    public function import_providers(Request $request)
    {
        $file_upload = $request->file('providers');
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

            //-- Create New Provider
            foreach ($data as $key => $value) {

                $input['name'] = $value['name'];

                $validator = Validator::make($input, $rules);
                if (!$validator->fails()) {

                    Provider::create([
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
