<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Database\Factories\TicketStatusHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketStatusHistory extends Model
{
    /** @use HasFactory<TicketStatusHistoryFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'ticket_id',
        'changed_by',
        'old_status',
        'new_status',
        'note',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_status' => TicketStatus::class,
            'new_status' => TicketStatus::class,
            'created_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
