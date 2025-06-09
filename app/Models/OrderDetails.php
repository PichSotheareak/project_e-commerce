<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order_details';

    protected $fillable = [
        'orders_id',
        'product_id',
        'quantity',
        'price',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function order(){
        return $this -> belongsTo(Orders::class);
    }
}
