<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Models\Template;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total'      => Submission::count(),
            'processing' => Submission::whereIn('status', ['queued', 'processing'])->count(),
            'completed'  => Submission::where('status', 'completed')->count(),
            'failed'     => Submission::where('status', 'failed')->count(),
            'templates'  => Template::count(),
        ];

        $recent = Submission::with(['contact', 'template'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent'));
    }
}
