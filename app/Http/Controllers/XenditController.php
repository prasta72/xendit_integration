<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class XenditController extends Controller
{
    public function createVirtualAccount(Request $request)
    {
        // Set Xendit API key
        $apiKey = 'xnd_development_gnrIugIwRQTA9OwNvx1NcyTm8ABc9DTuZZthWZqtlrYeIIEntL0RTdtdNKO58LEj:';

        // Your logic to create a Virtual Account using Xendit API
        // For example:
        $params = [
            'external_id' =>  (string) Str::uuid(), // Replace with your unique external_id
            'bank_code' => $request->bank_code,      // The bank code for the Virtual Account (BNI, BCA, Mandiri, etc.)
            'name' => $request->name,
            // 'is_single_use'  => true,    // Customer name
            'amount' => $request->amount, // The expected amount for the Virtual Account
        ];

           $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($apiKey)
            ])->post('https://api.xendit.co/v2/invoices', $params);
       
           return response()->json($response->json());
    }
}
