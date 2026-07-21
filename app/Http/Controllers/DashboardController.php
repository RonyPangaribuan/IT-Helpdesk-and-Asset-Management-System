<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = request()->user();

        $dashboard = match ($user->role) {
            User::ROLE_ADMIN => [
                'title' => 'Administrator Dashboard',
                'subtitle' => 'Operational control center placeholder for ticket intake, assignments, users, categories, and assets.',
                'cards' => [
                    ['label' => 'Ticket Review', 'value' => 'Milestone 2'],
                    ['label' => 'Technician Assignment', 'value' => 'Milestone 3'],
                    ['label' => 'Assets', 'value' => 'Milestone 5'],
                ],
                'next' => [
                    'Review all submitted tickets.',
                    'Assign open tickets to active technicians.',
                    'Manage users, categories, and asset records.',
                ],
            ],
            User::ROLE_TECHNICIAN => [
                'title' => 'Technician Dashboard',
                'subtitle' => 'Work queue placeholder for assigned tickets and resolution activity.',
                'cards' => [
                    ['label' => 'Assigned Tickets', 'value' => 'Milestone 3'],
                    ['label' => 'In Progress', 'value' => 'Milestone 3'],
                    ['label' => 'Resolved Work', 'value' => 'Milestone 4'],
                ],
                'next' => [
                    'View tickets assigned by an administrator.',
                    'Start work on assigned tickets.',
                    'Submit resolution notes after handling issues.',
                ],
            ],
            default => [
                'title' => 'Requester Dashboard',
                'subtitle' => 'Self-service placeholder for reporting issues and monitoring personal tickets.',
                'cards' => [
                    ['label' => 'My Tickets', 'value' => 'Milestone 2'],
                    ['label' => 'Open Reports', 'value' => 'Milestone 2'],
                    ['label' => 'Resolved Reports', 'value' => 'Milestone 4'],
                ],
                'next' => [
                    'Create a new IT support ticket.',
                    'Track the status of submitted tickets.',
                    'Confirm or reopen resolved tickets.',
                ],
            ],
        };

        return view('dashboard', [
            'dashboard' => $dashboard,
            'user' => $user,
        ]);
    }
}
