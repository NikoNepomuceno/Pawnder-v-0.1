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
                            <li class="active"><a href="{{ route('admin.reports') }}"><i class="fas fa-flag"></i>
                                    Reports</a></li>
                            <li><a href="{{ route('admin.flagged-posts') }}"><i class="fas fa-exclamation-triangle"></i>
                                    Flagged Posts</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Report Details</h5>
                        <a href="{{ route('admin.reports') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reports
                        </a>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="report-info mb-4">
                            <h6>Report Information</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 150px;">Report ID</th>
                                        <td>{{ $report->id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            @if($report->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($report->status == 'reviewed')
                                                <span class="badge bg-success">Reviewed</span>
                                            @else
                                                <span class="badge bg-secondary">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Reported By</th>
                                        <td>{{ $report->reporter->name }} ({{ $report->reporter->email }})</td>
                                    </tr>
                                    <tr>
                                        <th>Reported On</th>
                                        <td>{{ $report->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    @if($report->reviewed_at)
                                        <tr>
                                            <th>Reviewed On</th>
                                            <td>{{ $report->reviewed_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reviewed By</th>
                                            <td>{{ $report->reviewer->name ?? 'Unknown' }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <div class="report-reason mb-4">
                            <h6>Reason for Report</h6>
                            @if($report->hasViolationCategories())
                                <div class="violation-categories">
                                    @foreach($report->grouped_violation_reasons as $category => $reasons)
                                        <div class="category-group mb-3">
                                            <h6 class="category-title">
                                                @if($category === 'Content-Related Violations')
                                                    <i class="fas fa-exclamation-triangle text-danger"></i>
                                                @elseif($category === 'User Behavior Violations')
                                                    <i class="fas fa-user-times text-warning"></i>
                                                @else
                                                    <i class="fas fa-shield-alt text-info"></i>
                                                @endif
                                                {{ $category }}
                                            </h6>
                                            <div class="violation-badges">
                                                @foreach($reasons as $reason)
                                                    <span class="badge bg-secondary me-1 mb-1">{{ $reason }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-3 bg-light rounded">
                                    {{ $report->reason }}
                                </div>
                            @endif
                        </div>

                        @if($report->admin_notes)
                            <div class="admin-notes mb-4">
                                <h6>Admin Notes</h6>
                                <div class="p-3 bg-light rounded">
                                    {{ $report->admin_notes }}
                                </div>
                            </div>
                        @endif

                        @if($report->post)
                            <div class="reported-post mb-4">
                                <h6>Reported Post</h6>
                                <div class="card">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $report->post->user->profile_picture ?? asset('images/default-profile.png') }}"
                                                alt="Profile" class="post-avatar"
                                                style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                            <div>
                                                <h6 class="mb-0">{{ $report->post->name }}</h6>
                                                <small
                                                    class="text-muted">{{ $report->post->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h5>{{ $report->post->title }}</h5>
                                        <p>{{ $report->post->description }}</p>

                                        <div class="post-details mb-3">
                                            <span class="badge bg-info">Status: {{ ucfirst($report->post->status) }}</span>
                                            <span class="badge bg-info">Breed: {{ $report->post->breed }}</span>
                                            <span class="badge bg-info">Location: {{ $report->post->location }}</span>
                                        </div>

                                        @if(count($report->post->photo_urls) > 0)
                                            <div class="post-images mb-3">
                                                <div class="row">
                                                    @foreach($report->post->photo_urls as $index => $photo_url)
                                                        @if($index < 3)
                                                            <div class="col-md-4">
                                                                <img src="{{ $photo_url }}" alt="Pet Photo" class="img-fluid rounded">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                The reported post has been deleted or is no longer available.
                            </div>
                        @endif

                        @if($report->status == 'pending' && $report->post)
                            <div class="review-actions mb-4">
                                <h6>Review Actions</h6>
                                <form action="{{ route('admin.reports.review', $report->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="status">Decision</label>
                                        <select class="form-control" id="status" name="status" required>
                                            <option value="reviewed">Mark as Reviewed</option>
                                            <option value="rejected">Reject Report</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="admin_notes">Admin Notes</label>
                                        <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3"></textarea>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="flag_post" name="flag_post"
                                            value="1">
                                        <label class="form-check-label" for="flag_post">
                                            Flag this post as inappropriate
                                        </label>
                                    </div>

                                    <div class="form-group mb-3" id="flag_reason_container" style="display: none;">
                                        <label for="flag_reason">Flag Reason</label>
                                        <input type="text" class="form-control" id="flag_reason" name="flag_reason"
                                            placeholder="This post violates our community guidelines">
                                    </div>

                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </form>
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
            margin-right: 5px;
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

        .badge.bg-info {
            background-color: #17a2b8 !important;
        }

        /* Violation Categories Styling */
        .violation-categories {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #dee2e6;
        }

        .category-group {
            background: white;
            border-radius: 6px;
            padding: 15px;
            border: 1px solid #e9ecef;
        }

        .category-title {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .violation-badges .badge {
            font-size: 11px;
            padding: 4px 8px;
            margin: 2px;
        }

        .violation-summary {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .violation-count .badge {
            font-size: 10px;
            padding: 2px 6px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flagPostCheckbox = document.getElementById('flag_post');
            const flagReasonContainer = document.getElementById('flag_reason_container');

            if (flagPostCheckbox) {
                flagPostCheckbox.addEventListener('change', function () {
                    flagReasonContainer.style.display = this.checked ? 'block' : 'none';
                });
            }
        });
    </script>
@endsection