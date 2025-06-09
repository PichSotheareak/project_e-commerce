<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'customer_id',
        'transaction_date',
        'pick_up_date_time',
        'total_amount',
        'paid_amount',
        'status',
        'order_id',
        'payment_method_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function order(){
        return $this->belongsTo(Orders::class);
    }
    public function paymentMethod(){
        return $this->belongsTo(PaymentMethod::class);
    }
    public function invoiceItems(){
        return $this->hasMany(InvoiceItems::class);
    }

    public function payments(){
        return $this->hasMany(Payments::class);
    }
}
