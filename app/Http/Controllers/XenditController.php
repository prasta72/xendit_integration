<?php

namespace App\Http\Controllers;

use App\Models\payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Xendit\Xendit;


class XenditController extends Controller
{
    public  $apiKey = "";

    public function __construct()
    {
        $this->apiKey = base64_encode('api_key');
    }

    public function createInvoice(Request $request)
    {
//        
            $params = [
                'external_id' =>  (string) Str::uuid(), // Replace with your unique external_id
    //            'bank_code' => $request->bank_code,      // The bank code for the Virtual Account (BNI, BCA, Mandiri, etc.)
                'customer' => $request->customer,
                // 'is_single_use'  => true,    // Customer name
                'amount' => $request->amount,
                'success_redirect_url' => 'https://translate.google.com/'// The expected amount for the Virtual Account
            ];

            $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->apiKey
            ])->post('https://api.xendit.co/v2/invoices', $params);


           $payment = new payment();
           $payment->invoice_link = $response['invoice_url'];
           $payment->payment_id = $response['external_id'];
           $payment->status = 'pendding';
           $payment->save();

           return response()->json(['data' => $response['invoice_url']]);
    }

    public function webHook(Request $request){
        $id = $request->id;
        Xendit::setApiKey('apikey');
        $response = \Xendit\Invoice::retrieve($id);

        $payment = payment::where('payment_id', $response['external_id'])->firstOrFail();

        if($payment->status == 'settled'){
            return response()->json(['data' => 'payment as been already proceced']);
        }

        $payment->status = strtolower($response['status']);
        $payment->save();

        return response()->json(['data' => 'success']);

    }


    public function createVirtualAccount(Request $request){
        Xendit::setApiKey('api_key');
        $now = Carbon::now();
        $params = [
            'external_id' => (string) Str::uuid(),
            'bank_code' => $request->bank_code,
            'name' => $request->name,
            'is_closed' => true,
            'expected_amount' => $request->expected_amount,
            'expiration_date' => $now->addDays(1)->toISOString()
        ];

//        return response()->json($params);

        $response = \Xendit\VirtualAccounts::create($params);

        $payment = new payment();
        $payment->invoice_link = $response['account_number'];
        $payment->payment_id = $response['external_id'];
        $payment->status = 'active';
        $payment->save();

        return response()->json($response);
    }


    public function  vituralAcountHook(Request  $request){
            $payment = payment::where('payment_id', $request->external_id)->firstOrFail();
            if($payment){
                $payment->status = "PAID";
                $payment->save();
                return 'ok';
            }else{
                return response()->json([
                    'data' => 'error'
                ]);
            }
    }





}
