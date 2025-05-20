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
                            <li><a href="{{ route('admin.reports') }}"><i class="fas fa-flag"></i> Reports</a></li>
                            <li class="active"><a href="{{ route('admin.flagged-posts') }}"><i
                                        class="fas fa-exclamation-triangle"></i> Flagged Posts</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5>Flagged Posts</h5>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if($posts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Posted By</th>
                                            <th>Flag Reason</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($posts as $post)
                                            <tr>
                                                <td>{{ $post->id }}</td>
                                                <td>{{ Str::limit($post->title, 30) }}</td>
                                                <td>{{ $post->user->name }}</td>
                                                <td>{{ Str::limit($post->flag_reason, 30) }}</td>
                                                <td>{{ $post->updated_at->format('M d, Y') }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="/posts/{{ $post->id }}" class="btn btn-sm btn-info"
                                                            target="_blank">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <form action="{{ route('admin.posts.unflag', $post->id) }}" method="POST"
                                                            class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i> Unflag
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $posts->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                No flagged posts found.
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

        .btn-group .btn {
            margin-right: 5px;
        }
    </style>
@endsection