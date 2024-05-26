<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Karim007\LaravelBkashTokenize\Facade\BkashPaymentTokenize;

class BkashPaymentController extends Controller
{
    public function index()
{
    $orderId = session()->get('order_id');
    $order = Order::find($orderId);

    $inv = uniqid();
    $data['intent'] = 'sale';
    $data['mode'] = '0011';
    $data['payerReference'] = $inv;
    $data['currency'] = 'BDT';
    $data['amount'] = $order->total_amount;
    $data['merchantInvoiceNumber'] = $inv;
    $data['callbackURL'] = config("bkash.callbackURL");;
    $data_json = json_encode($data);

    $response =  BkashPaymentTokenize::cPayment($data_json);

    
    if (isset($response['bkashURL'])) return redirect()->away($response['bkashURL']);
    else return redirect()->back()->with('error', $response['statusMessage']);
    
    
}
    public function callback(Request $request){
        if ($request->status == 'success'){
            $response = BkashPaymentTokenize::executePayment($request->paymentID);
           
            if (!$response){
                $response =  BkashPaymentTokenize::queryPayment($request->paymentID);
               
            }
            
            if (isset($response['statusCode']) && $response['statusCode'] == "0000"
             && $response['transactionStatus'] == "Completed") {

        $orderId = session()->get('order_id');
        $order = Order::find($orderId);
        $order->status = 'Paid';
        $order->save();

        session()->forget('order_id');
        

    return redirect()->to('/dashboard')->with('success', 'Your payment has been successfully completed');
            }
        }else if ($request->status == 'cancel'){
            return redirect()->route('order.payment')->with('error', 'Your Payment is canceled');
        }else{
            return redirect()->route('order.payment')->with('error', 'Your Transaction is Failed');
        }
    } 

}
