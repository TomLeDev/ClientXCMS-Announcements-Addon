<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.title'))
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        <div class="card">
            <div class="card-heading">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.title') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.description') }}</p>
                </div>
                <div class="flex gap-2">
                    <a class="btn btn-primary" href="{{ route($routePath . '.create') }}">
                        <i class="bi bi-plus mr-1"></i>{{ __('admin.create') }}
                    </a>
                </div>
            </div>
            <div class="border rounded-lg overflow-hidden dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">#</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('announcements::messages.categories.fields.name') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('announcements::messages.categories.fields.slug') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('announcements::messages.categories.fields.announcements_count') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('global.status') }}</th>
                            <th class="px-6 py-3 text-start text-xs font-semibold uppercase text-gray-800 dark:text-gray-200">{{ __('global.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                            <tr class="bg-white hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-800">
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($item->icon)
                                            <i class="{{ $item->icon }}" style="color: {{ $item->color }}"></i>
                                        @else
                                            <span class="w-4 h-4 rounded" style="background-color: {{ $item->color }}"></span>
                                        @endif
                                        <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->slug }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->announcements_count ?? 0 }}</td>
                                <td class="px-6 py-4">
                                    @if($item->is_active)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">{{ __('global.active') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300">{{ __('global.inactive') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route($routePath . '.show', ['category' => $item]) }}" class="btn-icon"><i class="bi bi-pencil"></i></a>
                                        <form method="POST" action="{{ route($routePath . '.destroy', ['category' => $item]) }}" class="inline confirmation-popup">
                                            @method('DELETE')@csrf
                                            <button type="submit" class="btn-icon text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-white dark:bg-slate-900">
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">{{ __('global.no_results') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="py-1 px-4">{{ $items->links('admin.shared.layouts.pagination') }}</div>
        </div>
    </div>
@endsection
