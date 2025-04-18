<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Provider;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //----------------- dashboard_data -----------------------\\

    public function dashboard_data(Request $request): \Illuminate\Http\JsonResponse
    {
        $user_auth = auth()->user();

        $invoices = Invoice::count();
        $clients = Client::count();
        $suppliers = Provider::count();

        return response()->json([
            'invoices' => $invoices,
            'clients' => $clients,
            'suppliers' => $suppliers,
        ]);

    }
}
