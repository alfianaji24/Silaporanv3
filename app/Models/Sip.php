<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sip extends Model
{
    use HasFactory;

    protected $table = 'sip';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
