<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'profile';

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'image',
        'type',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
