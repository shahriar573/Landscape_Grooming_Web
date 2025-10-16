<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorRevision extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'vendor_id',
        'collab_event_id',
        'created_by',
        'revision_key',
        'before_state',
        'after_state',
        'created_at',
    ];

    protected $casts = [
        'before_state' => 'array',
        'after_state' => 'array',
        'created_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(CollabEvent::class, 'collab_event_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
