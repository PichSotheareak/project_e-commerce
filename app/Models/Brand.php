<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'image', 'created_at', 'updated_at', 'deleted_at'];

    public function product(){
        return $this->hasMany(Product::class);
    }
}
