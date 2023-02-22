<?php
namespace App\Helpers;

use Validator;
use DB;
use Illuminate\Support\Arr;
use Auth;
class OrderHelper
{
	public static function processOrder($data)
	{
		$order = \App\Models\Order::find($data);
		
		if(!empty($order->order_placed)){
			throw new \App\Exceptions\DataException("This order has already been confirmed", 400);
		}
		if(!empty($order->order_cancelled)){
			throw new \App\Exceptions\DataException("This order has already been cancelled", 400);
		}
		if($order->status != config('constants.status.active')){
			throw new \App\Exceptions\DataException("This order is not active anymore", 400);
		}

		$itemData = \App\Models\Product::whereIn('id', function($query) use ($data){
			$query->select('product_id')
						->from(with(new \App\Models\OrderProduct)->getTable())
						->where('order_id', $data)
						->whereNull('deleted_at')
						->where('status', config('constants.status.active'));
		})->get()->keyBy('id')->toArray();
		
		\DB::beginTransaction();
		$orderTotal = 0;
		
		foreach ($itemData as $key => $value) {

			$item = \App\Models\OrderProduct::where('order_id', $data)->where('product_id', $key)->first();
			$item->product_name = $value['name'];
			$item->product_price = $value['price'];
			$item->total_price = $value['price']*$item->quantity;

			try {
				$item->save();
				$orderTotal = $orderTotal+$item->total_price;
			} catch (\Exception $e) {
				\DB::rollBack();
				throw new \App\Exceptions\DBOException("Error Processing Request : ".$e->getMessage(), 500);
			}
		}

		$order->order_total = $orderTotal;
		$order->save();

		\DB::commit();
		return $order;
	}

	public static function processPayment($inputs, $preparedOrder)
	{
		if(!empty($inputs['payment_method'])){
            $paymentMethod = $inputs['payment_method'];
        } else{
            $paymentMethod = 'superpay';
        }
        $paymentHelper = config('constants.payment_helpers.'.$paymentMethod);
        $paymentProcessor = new $paymentHelper;
        $processPayment = $paymentProcessor->processPayment($preparedOrder);

        return $processPayment;
	}

	public static function markOrderAsPaid($order)
	{
		$order->order_placed = \Carbon\Carbon::now();
		$order->save();
		return $order;
	}
}
