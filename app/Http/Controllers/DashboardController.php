<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    /**
     * Display the dashboard analytics page
     */
    public function index()
    {
        $user = auth()->user();

        // Check if user is Admin or Super Admin
        if ($user->role && in_array($user->role->slug, ['super-admin', 'admin'])) {
            $overview = $this->dashboardService->adminOverview();

            return view('pages.dashboard.admin', [
                'overview' => $overview,
                'chartData' => $this->dashboardService->chartPayload($overview),
            ]);
        }

        // Default dashboard for non-admin users
        return view('pages.dashboard.user');
    }
}
