@extends('layouts.admin')

@section('content')
<div id="pageFade" class="flex min-h-screen opacity-0 transition-opacity duration-500 animate-bounce">
    <x-admin-side-bar />
    <div class="flex-1 p-8">
        <h1>Archived Reports</h1>
        <div class="reports-section">
            <p>This is where archived reports will be displayed.</p>
            {{-- Placeholder for archived reports table --}}
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            var el = document.getElementById('pageFade');
            el.classList.add('opacity-100');
            setTimeout(function () {
                el.classList.remove('animate-bounce');
            }, 1000);
        }, 10);
    });
</script>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush 