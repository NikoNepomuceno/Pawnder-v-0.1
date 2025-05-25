@extends('layouts.admin')

@section('content')
<div id="pageFade" class="flex min-h-screen opacity-0 transition-opacity duration-500">
    <x-admin-side-bar />
    <div class="flex-1 p-8">
        <div class="dashboard-header-row mb-6">
            <h1>Approved Reports</h1>
        </div>
        <div class="reports-container inline-block mx-auto max-w-fit bg-white border border-gray-200 rounded-xl shadow">
            <div class="reports-section">
                <div class="section-header">
                    <h2><i class="fas fa-check-circle"></i> Approved Reports</h2>
                </div>
                <div class="w-full">
                    <table class="min-w-full w-full bg-white">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Report ID
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Report Created
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Approved At
                                </th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    Approved By
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($approvedReports as $report)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ $report->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $report->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $report->reviewed_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Admin
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        No approved reports found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($approvedReports->hasPages())
                    <div class="mt-4">
                        {{ $approvedReports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
            document.getElementById('pageFade').classList.add('opacity-100');
        }, 10);
    });
</script>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush 