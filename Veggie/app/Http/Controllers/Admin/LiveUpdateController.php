<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class LiveUpdateController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'latest_order_id' => Order::max('id'),
            'latest_contact_id' => Contact::max('id'),
        ]);
    }
}
