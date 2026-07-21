<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Reopened = 'reopened';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Assigned => 'Assigned',
            self::InProgress => 'In Progress',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
            self::Reopened => 'Reopened',
            self::Cancelled => 'Cancelled',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Cancelled, self::Closed], true);
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, self::transitionMap()[$this->value] ?? [], true);
    }

    public function canTransitionToInMilestoneThree(self $target): bool
    {
        return in_array($target, self::milestoneThreeTransitionMap()[$this->value] ?? [], true);
    }

    /**
     * @return array<string, array<int, self>>
     */
    public static function transitionMap(): array
    {
        return [
            self::Open->value => [self::Assigned, self::Cancelled],
            self::Assigned->value => [self::InProgress, self::Cancelled],
            self::InProgress->value => [self::Resolved],
            self::Resolved->value => [self::Closed, self::Reopened],
            self::Reopened->value => [self::Assigned, self::InProgress],
            self::Cancelled->value => [],
            self::Closed->value => [],
        ];
    }

    /**
     * @return array<string, array<int, self>>
     */
    public static function milestoneThreeTransitionMap(): array
    {
        return [
            self::Open->value => [self::Assigned, self::Cancelled],
            self::Assigned->value => [self::InProgress, self::Cancelled],
            self::InProgress->value => [],
            self::Resolved->value => [],
            self::Reopened->value => [],
            self::Cancelled->value => [],
            self::Closed->value => [],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
