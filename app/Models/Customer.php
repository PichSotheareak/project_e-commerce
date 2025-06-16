<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';
    protected $hidden = ['password'];
    protected $fillable = [
        'name',
        'gender',
        'email',
        'phone',
        'address',
        'password',
        'image',
        'create_at',
        'update_at',
        'delete_at',
    ];

    public function orders(){
        return $this -> hasMany(Orders::class, 'customer_id', 'id');
    }
    public function invoices(){
        return $this -> hasMany(Invoices::class, 'customer_id', 'id');
    }
}
