<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function dashboard(Request $request): View
    {
        $stats = [
            'customers' => \App\Models\User::where('role', 'user')->count(),
            'technicians' => \App\Models\User::where('role', 'technician')->count(),
            'orders' => 0,
            'repairs' => \App\Models\RepairSubmission::count(),
            'repairs_new' => \App\Models\RepairSubmission::where('status', 'new')->count(),
            'contact_new' => \App\Models\ContactSubmission::where('status', 'new')->count(),
        ];

        $recentRepairs = \App\Models\RepairSubmission::orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'repair_number', 'name', 'email', 'device', 'brand', 'model', 'status', 'created_at']);

        return view('admin.dashboard', compact('stats', 'recentRepairs'));
    }
}

