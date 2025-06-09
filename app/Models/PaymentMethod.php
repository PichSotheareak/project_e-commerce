<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'payment_methods';
    protected $fillable = ['name', 'account_number', 'qrcode', 'create_at', 'update_at', 'deleted_at'];

    public function invoices(){
        return $this->hasMany(Invoices::class);
    }

    public function payments(){
        return $this->hasMany(Payments::class);
    }

}
