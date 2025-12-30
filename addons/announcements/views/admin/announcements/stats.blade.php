<?php
/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.stats_title'))
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewsData = @json($stats['views_by_day']);
            const likesData = @json($stats['likes_by_day']);
            
            new Chart(document.getElementById('viewsChart'), {
                type: 'line',
                data: {
                    labels: viewsData.map(d => d.date),
                    datasets: [{
                        label: '{{ __('announcements::messages.admin.fields.views') }}',
                        data: viewsData.map(d => d.count),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
            
            new Chart(document.getElementById('likesChart'), {
                type: 'line',
                data: {
                    labels: likesData.map(d => d.date),
                    datasets: [{
                        label: '{{ __('announcements::messages.admin.fields.likes') }}',
                        data: likesData.map(d => d.count),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        });
    </script>
@endsection
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        
        <div class="card">
            <div class="card-heading">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.stats_title') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $item->title }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <form method="GET" class="flex items-center gap-2">
                        <select name="period" class="input-text" onchange="this.form.submit()">
                            @foreach(__('announcements::messages.admin.stats.periods') as $value => $label)
                                <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route($routePath . '.export-stats', ['announcement' => $item, 'period' => $period]) }}" class="btn btn-secondary">
                        <i class="bi bi-download mr-1"></i>{{ __('announcements::messages.admin.stats.export_csv') }}
                    </a>
                    <a href="{{ route($routePath . '.show', $item) }}" class="btn btn-primary">
                        <i class="bi bi-pencil mr-1"></i>{{ __('global.edit') }}
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-blue-100 dark:bg-blue-800 rounded-lg"><i class="bi bi-eye text-2xl text-blue-600 dark:text-blue-400"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($stats['total_views']) }}</div>
                            <div class="text-sm text-gray-500">{{ __('announcements::messages.admin.stats.total_views') }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-green-100 dark:bg-green-800 rounded-lg"><i class="bi bi-person-check text-2xl text-green-600 dark:text-green-400"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($stats['unique_views']) }}</div>
                            <div class="text-sm text-gray-500">{{ __('announcements::messages.admin.stats.unique_views') }}</div>
                        </div>
                    </div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-red-100 dark:bg-red-800 rounded-lg"><i class="bi bi-heart text-2xl text-red-600 dark:text-red-400"></i></div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($stats['total_likes']) }}</div>
                            <div class="text-sm text-gray-500">{{ __('announcements::messages.admin.stats.total_likes') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 pt-0">
                <div class="border dark:border-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('announcements::messages.admin.stats.views_chart') }}</h3>
                    <div style="height: 250px;"><canvas id="viewsChart"></canvas></div>
                </div>
                <div class="border dark:border-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('announcements::messages.admin.stats.likes_chart') }}</h3>
                    <div style="height: 250px;"><canvas id="likesChart"></canvas></div>
                </div>
            </div>
            
            @if(count($stats['top_referers']) > 0)
                <div class="p-4 pt-0">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('announcements::messages.admin.stats.top_referers') }}</h3>
                    <div class="border dark:border-gray-700 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referer</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('announcements::messages.admin.fields.views') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($stats['top_referers'] as $referer)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 truncate max-w-md">{{ $referer->referer }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-right">{{ number_format($referer->count) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
