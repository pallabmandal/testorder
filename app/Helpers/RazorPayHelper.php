<?php
namespace App\Helpers;

use Validator;
use DB;
use Illuminate\Support\Arr;
use \Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Auth;

class RazorPayHelper
{
	public function processPayment($data)
	{
		$orderPayment = new \App\Models\OrderPayment();
		$orderPayment->order_id = $data->id;
		$orderPayment->payment_method = 'razorpay';
		$orderPayment->payment_response = "Payment setup is not complete";
		$orderPayment->payment_time = Carbon::now();
		$orderPayment->status = config('constants.status.payment_rejected');
		

		$orderPayment->save();

		return $orderPayment;
	}
}
