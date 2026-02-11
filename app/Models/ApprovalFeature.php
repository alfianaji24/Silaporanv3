<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_feature',
        'feature',
        'name',
        'description'
    ];

}
