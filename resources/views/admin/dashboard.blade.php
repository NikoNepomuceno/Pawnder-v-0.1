@extends('layouts.admin')

@section('content')
    <div class="admin-dashboard">
        <div class="dashboard-header" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h1>Admin Dashboard</h1>
                <livewire:admin.dashboard-stats />
            </div>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin-left: 2rem;">
                @csrf
                <button type="submit" class="admin-logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>

        <div class="reports-section">
            <h2>Reported Posts</h2>
            <livewire:admin.reports-table />
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
    <style>
        .admin-logout-btn {
            background: var(--admin-primary);
            color: #fff;
            border: none;
            border-radius: 0.5rem;
            padding: 0.6rem 1.3rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            box-shadow: 0 2px 8px rgba(255, 152, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 0.5em;
        }

        .admin-logout-btn:hover {
            background: var(--admin-primary-dark);
        }
    </style>
@endpush