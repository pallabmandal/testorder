<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;

    protected $appends = ['status_name'];

    public function getStatusNameAttribute() 
    {
        return config('constants.status.'.$this->status);
    }
}
