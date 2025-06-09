<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orders extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'orders';

    protected $fillable = ['customers_id',
        'users_id',
        'branches_id',
        'order_date',
        'total_amount',
        'status',
        'payment_status',
        'remarks',
        'created_at',
        'updated_at',
        'deleted_at'];

    public function branches(){
        return $this->belongsTo(Branches::class, 'branches_id');
    }

    public function users(){
        return $this->belongsTo(User::class, 'users_id');
    }

    public function customers(){
        return $this->belongsTo(Customer::class, 'customers_id');
    }

    public function order_details(){
        return $this->hasMany(OrderDetails::class, 'orders_id');
    }

    public function invoices(){
        return $this->hasMany(Invoices::class, 'orders_id');
    }
}
