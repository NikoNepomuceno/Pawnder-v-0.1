<?php

namespace App\Http\Controllers;

use App\Models\PostReport;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function showReport($id)
    {
        $report = PostReport::with(['post', 'reporter'])->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }
}
