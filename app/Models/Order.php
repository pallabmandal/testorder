<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_placed',
        'order_cancelled',
        'status'
    ];

    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class,'order_products')->withTimestamps();
    }

    public function orderProduct()
    {
        return $this->hasMany(\App\Models\OrderProduct::class);
    }

    public function payment()
    {
        return $this->hasMany(\App\Models\OrderPayment::class)->orderBy('id', 'DESC');
    }

    public function scopeActive($q)
    {
        return $q->where('status', config('constants.status.active'));
    }

    public function scopeBelongsToUser($q)
    {
        return $q->where('user_id', Auth::user()->id);
    }

    public function scopeNotPaid($q)
    {
        return $q->whereNull('order_placed');
    }

    public function scopePaid($q)
    {
        return $q->whereNotNull('order_placed');
    }

    public function scopeNotCancelled($q)
    {
        return $q->whereNull('order_cancelled');
    }

    public function scopeCancelled($q)
    {
        return $q->whereNotNull('order_cancelled');
    }

    public function getPastOrderById($request)
    {
        $data = $this->belongsToUser()->active()
        ->with(
            [
                'payment',
                'orderProduct',
                'orderProduct.product:id,name,price'
            ]
        )
        ->where('id', $request->order_id)
        ->first();

        return $data;
    }

    public function searchOpenOrders($request)
    {
        $data = $this->with(
            [
                'orderProduct',
                'orderProduct.product:id,name,price'
            ]
        )->belongsToUser()->active()->notPaid()->notCancelled()->first();

        return $data;
    }

    public function searchClosedeOrders($request)
    {
        $pageSize = $request->input('page_size', '20');
        $inputs = $request->all();

        $data = $this->belongsToUser()->active();

        if(!empty($inputs['type']) && in_array('completed', $inputs['type'])){
            $data = $data->paid();
        }

        if(!empty($inputs['type']) && in_array('cancelled', $inputs['type'])){
            $data = $data->cancelled();
        }

        if(!empty($inputs['start_date'])){
            $data = $data->where('order_placed', '>=', $inputs['start_date']);
        }

        if(!empty($inputs['end_date'])){
            $data = $data->where('order_placed', '<=', $inputs['end_date']);
        }

        $data = $data->paginate($pageSize);

        return $data;
    }

    public function addItemToOrders($request)
    {
        $inputs = $request->all();

        $order = $this->belongsToUser()->active()->notPaid()->notCancelled()->first();

        if(empty($order)){
            //Create a new order
            $order = $this->create(
                [
                    'user_id' => Auth::user()->id
                ]
            );
            //add item to order
        }

        $item = $order->addItem($inputs['product_id']);

        return $item;
    
    }

    public function addItem($data)
    {
        //check if the item is already added to cart
        $item = \App\Models\OrderProduct::where('order_id', $this->id)->where('product_id', $data)->first();
        if(empty($item)){
            $item = $this->products()->attach($data, ['quantity' => 1]);
        } else {
            $item->quantity = $item->quantity+1;
            $item->save();
        }
        return $item;
    }
}
