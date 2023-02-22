<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    public function orderProduct()
    {
        return $this->hasOne(\App\Models\OrderProduct::class);
    }

    protected $fillable = [
        'name',
        'price'
    ];

    public function getPriceAttribute($value)
    {
        return number_format((float)$value, 2, '.', '');;
    }

    public function scopeActive($q)
    {
        return $q->where('status', config('constants.status.active'));
    }

    public function searchProducts($request)
    {
        $pageSize = $request->input('page_size', '20');
        $inputs = $request->all();

        $data = $this->active();

        if(!empty($inputs['search'])){
            $data = $data->where('name', 'LIKE', '%'.$inputs['search'].'%');
        }

        $sortOrder = $request->input('sort_order', 'ASC');
        $sortBy    = $request->input('sort_by', 'name');
        $data = $data->orderBy($sortBy, $sortOrder);

        $data = $data->paginate($pageSize);

        return $data;
    }
}
