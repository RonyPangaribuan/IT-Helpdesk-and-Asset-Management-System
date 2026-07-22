<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_code',
        'requester_id',
        'technician_id',
        'ticket_category_id',
        'asset_id',
        'title',
        'description',
        'location',
        'priority',
        'status',
        'resolution_note',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public static function pendingCode(): string
    {
        return 'PENDING-'.Str::uuid()->toString();
    }

    public static function codeFromId(int $id, ?int $year = null): string
    {
        return sprintf('TCK-%d-%06d', $year ?? (int) now()->format('Y'), $id);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'ticket_category_id')->withTrashed();
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class)->withTrashed();
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)
            ->orderBy('created_at')
            ->orderBy('id');
    }

    public function isOpenAndUnassigned(): bool
    {
        return $this->status === TicketStatus::Open && $this->technician_id === null;
    }

    public function isReadOnly(): bool
    {
        return $this->status?->isTerminal() ?? false;
    }

    public function isCollaborationOpen(): bool
    {
        return ! $this->isReadOnly();
    }

    public function isRequesterEditable(): bool
    {
        return $this->isOpenAndUnassigned();
    }

    public function isAdminEditable(): bool
    {
        return in_array($this->status, [
            TicketStatus::Open,
            TicketStatus::Assigned,
            TicketStatus::InProgress,
            TicketStatus::Reopened,
        ], true);
    }
}
