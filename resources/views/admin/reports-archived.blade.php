@extends('layouts.admin')

@section('content')
    <div class="admin-dashboard">
        <h1>Archived Reports</h1>
        <div class="reports-section">
            <p>This is where archived reports will be displayed.</p>
            {{-- Placeholder for archived reports table --}}
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush 