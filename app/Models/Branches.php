<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branches extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function staff(){
        return $this->hasMany(Staff::class, 'branch_id', 'id');
    }

    public function orders(){
        return $this->hasMany(Orders::class);
    }
    public function branches(){
        return $this->hasMany(Branches::class);
    }
}
