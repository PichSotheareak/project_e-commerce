<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payments extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method_id',
        'branch_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function invoices(){
        return $this->belongsTo(Invoices::class);
    }

    public function payment_methods(){
        return $this->belongsTo(PaymentMethod::class);
    }
    public function branch(){
        return $this->belongsTo(Branches::class);
    }
}
