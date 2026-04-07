<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengirim',
        'penerima',
        'pesan',
        'status',
        'message_id',
        'error_message',
        'attempts',
        'last_attempt_at',
        'permanent_failed'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
