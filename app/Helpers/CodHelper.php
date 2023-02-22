<?php
namespace App\Helpers;

use Validator;
use DB;
use Illuminate\Support\Arr;
use \Carbon\Carbon;

class CodHelper
{
	public function processPayment($data)
	{
		$orderPayment = new \App\Models\OrderPayment();
		$orderPayment->order_id = $data['order_id'];
		$orderPayment->payment_method = 'cod';
		$orderPayment->payment_response = 'COD payment automatically mark as successfull';
		$orderPayment->payment_time = Carbon::now();
		$orderPayment->status = config('constants.status.payment_completed');
		$orderPayment->save();

		return $orderPayment;
	}
}
