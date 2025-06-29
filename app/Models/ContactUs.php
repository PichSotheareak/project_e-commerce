<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactUs extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'contact_us';
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'message',
    ];
    protected $dates = ['created_at', 'updated_at'];
}
