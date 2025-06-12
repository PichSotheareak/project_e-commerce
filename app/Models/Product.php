<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'name',
        'description',
        'image',
        'cost',
        'price',
        'inStock',
        'product_details_id',
        'categories_id',
        'brands_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function categories(){
        return $this->belongsTo(Category::class);
    }
    public function brands(){
        return $this->belongsTo(Brand::class);
    }
    public function product_details(){
        return $this->belongsTo(ProductDetails::class);
    }

    public function order_details(){
        return $this->belongsTo(OrderDetails::class);
    }
    public function invoiceItems(){
        return $this->hasMany(InvoiceItems::class);
    }
}
