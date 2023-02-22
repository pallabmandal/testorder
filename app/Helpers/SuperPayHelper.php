<?php
namespace App\Helpers;

use Validator;
use DB;
use Illuminate\Support\Arr;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Auth;

class SuperPayHelper
{
	public function processPayment($data)
	{
		$amount = $data->order_total;
		$orderPayment = new \App\Models\OrderPayment();
		$orderPayment->order_id = $data->id;
		$orderPayment->payment_method = 'superpay';
		$orderPayment->payment_time = Carbon::now();
		try {		
			$response = Http::post('https://superpay.view.agentur-loop.com/pay', 
				[
			    	'order_id' => $data->id,
			    	'customer_email' => Auth::user()->email,
			    	'value' => $amount,
				]
			);
		} catch (\Exception $e) {
			$orderPayment->payment_response = $e->getMessage();
			$orderPayment->status = config('constants.status.payment_rejected');
			$orderPayment->save();
			return $orderPayment;
		}

		$resp = json_decode($response->getBody(), true);

		$orderPayment->payment_response = $resp['message'];

		if($resp['message'] == 'Payment Successful'){
			$orderPayment->status = config('constants.status.payment_completed');
		} else {
			$orderPayment->status = config('constants.status.payment_rejected');
		}

		$orderPayment->save();

		return $orderPayment;
	}
}
