<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class role_user extends Model
{
    use HasFactory;

    protected $table = 'role_user';

    protected $fillable = [
        'user_id', 'role_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'role_id' => 'integer',
    ];
}
