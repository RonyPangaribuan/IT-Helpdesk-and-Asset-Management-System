<?php

namespace App\Services;

use App\Enums\TicketStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserManagementService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
            'password' => $data['password'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateUser(User $actor, User $user, array $data): User
    {
        $this->validateUpdate($actor, $user, $data);

        return DB::transaction(function () use ($data, $user): User {
            $updates = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'],
                'is_active' => (bool) ($data['is_active'] ?? $user->is_active),
            ];

            if (! empty($data['password'])) {
                $updates['password'] = $data['password'];
            }

            $user->update($updates);

            return $user->refresh();
        });
    }

    public function deactivateOwnAccount(User $user): User
    {
        if ($user->isAdmin()) {
            throw ValidationException::withMessages([
                'password' => 'Administrator accounts must be managed by another active administrator.',
            ])->errorBag('userDeletion');
        }

        if ($this->hasActiveAssignedTickets($user)) {
            throw ValidationException::withMessages([
                'password' => 'This technician still has active assigned tickets.',
            ])->errorBag('userDeletion');
        }

        $user->forceFill(['is_active' => false])->save();

        return $user->refresh();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateUpdate(User $actor, User $user, array $data): void
    {
        $newRole = (string) $data['role'];
        $newActive = (bool) ($data['is_active'] ?? $user->is_active);

        if ($actor->is($user) && ! $newActive) {
            throw ValidationException::withMessages([
                'is_active' => 'You cannot deactivate your own administrator account.',
            ]);
        }

        if ($actor->is($user) && $user->isAdmin() && $newRole !== User::ROLE_ADMIN) {
            throw ValidationException::withMessages([
                'role' => 'You cannot change your own administrator role.',
            ]);
        }

        if ($user->isAdmin() && $user->is_active && ($newRole !== User::ROLE_ADMIN || ! $newActive)) {
            $activeAdmins = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->where('is_active', true)
                ->whereKeyNot($user->id)
                ->count();

            if ($activeAdmins === 0) {
                $field = $newRole !== User::ROLE_ADMIN ? 'role' : 'is_active';

                throw ValidationException::withMessages([
                    $field => 'At least one active administrator must remain.',
                ]);
            }
        }

        if ($user->isTechnician() && ! $newActive && $this->hasActiveAssignedTickets($user)) {
            throw ValidationException::withMessages([
                'is_active' => 'This technician still has active assigned tickets.',
            ]);
        }

        if ($user->isRequester() && $newRole !== User::ROLE_REQUESTER && $user->requestedTickets()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'A requester with ticket history cannot be changed to another role.',
            ]);
        }

        if ($user->isTechnician() && $newRole !== User::ROLE_TECHNICIAN && $user->assignedTickets()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'role' => 'A technician with assigned ticket history cannot be changed to another role.',
            ]);
        }
    }

    private function hasActiveAssignedTickets(User $user): bool
    {
        if (! $user->isTechnician()) {
            return false;
        }

        return $user->assignedTickets()
            ->whereIn('status', [
                TicketStatus::Assigned->value,
                TicketStatus::InProgress->value,
                TicketStatus::Reopened->value,
            ])
            ->exists();
    }
}
