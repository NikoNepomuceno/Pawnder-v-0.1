@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="card admin-sidebar">
                    <div class="card-header">Admin Panel</div>

                    <div class="card-body">
                        <ul class="admin-menu">
                            <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            </li>
                            <li class="active"><a href="{{ route('admin.reports') }}"><i class="fas fa-flag"></i> Reports
                                    <span class="badge">{{ $reports->where('status', 'pending')->count() }}</span></a></li>
                            <li><a href="{{ route('admin.flagged-posts') }}"><i class="fas fa-exclamation-triangle"></i>
                                    Flagged Posts</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5>Post Reports</h5>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->query('status') == '' ? 'active' : '' }}"
                                    href="{{ route('admin.reports') }}">All Reports</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->query('status') == 'pending' ? 'active' : '' }}"
                                    href="{{ route('admin.reports', ['status' => 'pending']) }}">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->query('status') == 'reviewed' ? 'active' : '' }}"
                                    href="{{ route('admin.reports', ['status' => 'reviewed']) }}">Reviewed</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->query('status') == 'rejected' ? 'active' : '' }}"
                                    href="{{ route('admin.reports', ['status' => 'rejected']) }}">Rejected</a>
                            </li>
                        </ul>

                        @if($reports->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Post</th>
                                            <th>Reported By</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $report)
                                            <tr>
                                                <td>{{ $report->id }}</td>
                                                <td>
                                                    @if($report->post)
                                                        <strong>{{ Str::limit($report->post->title, 30) }}</strong>
                                                    @else
                                                        <span class="text-muted">Post deleted</span>
                                                    @endif
                                                </td>
                                                <td>{{ $report->reporter->name }}</td>
                                                <td>
                                                    @if($report->status == 'pending')
                                                        <span class="badge bg-warning text-dark">Pending</span>
                                                    @elseif($report->status == 'reviewed')
                                                        <span class="badge bg-success">Reviewed</span>
                                                    @else
                                                        <span class="badge bg-secondary">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>{{ $report->created_at->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.reports.show', $report->id) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $reports->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                No reports found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .admin-sidebar {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .admin-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .admin-menu li {
            padding: 10px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .admin-menu li.active {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .admin-menu li a {
            color: #333;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .admin-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .badge {
            background-color: #e41e3f;
            color: white !important;
            border-radius: 12px;
            padding: 3px 8px;
            font-size: 12px;
            margin-left: 10px;
        }

        .badge.bg-warning {
            background-color: #ffc107 !important;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
        }
    </style>
@endsection