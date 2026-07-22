<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboardService): View
    {
        $user = $request->user();

        return view('dashboard', [
            'dashboard' => $dashboardService->forUser($user),
            'user' => $user,
        ]);
    }
}
