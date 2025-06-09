<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['model',
        'processor',
        'ram',
        'storage',
        'display',
        'graphics',
        'os',
        'battery',
        'weight',
        'warranty',
        'created_at',
        'updated_at',
        'deleted_at'];

    public function product(){
        return $this->hasMany(Product::class);
    }
}
