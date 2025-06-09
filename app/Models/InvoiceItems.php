<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItems extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'price',
        'sub_total',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function invoice(){
        return $this->belongsTo(Invoices::class);
    }
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
