<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IPUnblockRequest extends Model
{
    use HasFactory;
    
    protected $table = 'ip_unblock_requests';
    protected $guarded = [];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the blacklist info for this unblock request
     */
    public function blacklistInfo()
    {
        return $this->belongsTo(IPBlacklist::class, 'ip_address', 'ip_address');
    }
}
