<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name',
        'gender',
        'email',
        'phone',
        'profile',
        'current_address',
        'position',
        'salary',
        'branches_id',
        'created_at',
        'updated_at',
        'deleted_at'];

    public function branches(){
        return $this->belongsTo(Branches::class);
    }
}
