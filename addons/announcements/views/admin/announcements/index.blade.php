<?php
/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.title'))
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="card">
                        <div class="card-heading">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                                    {{ __($translatePrefix . '.title') }}
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ __($translatePrefix . '.description') }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <a class="btn btn-secondary" href="{{ route($routePath . '.reorder') }}">
                                    <i class="bi bi-arrows-move mr-1"></i>
                                    {{ __('announcements::messages.admin.actions.reorder') }}
                                </a>
                                <a class="btn btn-primary" href="{{ route($routePath . '.create') }}">
                                    <i class="bi bi-plus mr-1"></i>
                                    {{ __('admin.create') }}
                                </a>
                            </div>
                        </div>

                        {{-- Filters --}}
                        <div class="p-4 border-b dark:border-gray-700">
                            <form method="GET" class="flex flex-wrap gap-4 items-end">
                                <div class="flex-1 min-w-[200px]">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('global.search') }}
                                    </label>
                                    <input type="text" name="q" value="{{ request('q') }}" 
                                        class="input-text" placeholder="{{ __('global.search') }}...">
                                </div>
                                <div class="w-48">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('global.status') }}
                                    </label>
                                    <select name="filter[status]" class="input-text">
                                        <option value="">{{ __('announcements::messages.all') }}</option>
                                        @foreach($filters as $value => $label)
                                            <option value="{{ $value }}" {{ (request('filter')['status'] ?? '') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-48">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('announcements::messages.admin.fields.category') }}
                                    </label>
                                    <select name="filter[category_id]" class="input-text">
                                        <option value="">{{ __('announcements::messages.all') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (request('filter')['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-secondary">
                                    <i class="bi bi-search mr-1"></i>
                                    {{ __('global.filter') }}
                                </button>
                                @if(request('q') || request('filter'))
                                    <a href="{{ route($routePath . '.index') }}" class="btn btn-light">
                                        <i class="bi bi-x-lg mr-1"></i>
                                        {{ __('global.reset') }}
                                    </a>
                                @endif
                            </form>
                        </div>

                        <div class="border rounded-lg overflow-hidden dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                #
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ __('global.title') }}
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ __('announcements::messages.admin.fields.category') }}
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ __('global.status') }}
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ __('announcements::messages.admin.fields.views') }} / {{ __('announcements::messages.admin.fields.likes') }}
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ __('announcements::messages.admin.fields.published_at') }}
                                            </span>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start">
                                            <span class="text-xs font-semibold uppercase tracking-wide text-gray-800 dark:text-gray-200">
                                                {{ __('global.actions') }}
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($items as $item)
                                        <tr class="bg-white hover:bg-gray-50 dark:bg-slate-900 dark:hover:bg-slate-800">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $item->id }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    @if($item->cover_image_display_url)
                                                        <img src="{{ $item->cover_image_display_url }}" alt="" class="w-10 h-10 rounded object-cover">
                                                    @endif
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                                {{ $item->title }}
                                                            </span>
                                                            @if($item->featured)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                                    <i class="bi bi-star-fill mr-1"></i>
                                                                    {{ __('announcements::messages.front.featured') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            /{{ setting('announcements_public_url', 'announcements') }}/{{ $item->slug }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($item->category)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                        style="background-color: {{ $item->category->color }}20; color: {{ $item->category->color }}">
                                                        {{ $item->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status_badge_class }}">
                                                    {{ $item->status_label }}
                                                </span>
                                                @if($item->isScheduled())
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <i class="bi bi-clock"></i>
                                                        {{ $item->published_at->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                                    <span title="{{ __('announcements::messages.admin.fields.views') }}">
                                                        <i class="bi bi-eye"></i> {{ number_format($item->views_count) }}
                                                    </span>
                                                    <span title="{{ __('announcements::messages.admin.fields.likes') }}">
                                                        <i class="bi bi-heart"></i> {{ number_format($item->likes_count) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $item->published_at?->format('d/m/Y H:i') ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route($routePath . '.show', $item) }}" class="btn-icon" title="{{ __('global.edit') }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="{{ route($routePath . '.stats', $item) }}" class="btn-icon" title="{{ __('announcements::messages.admin.actions.stats') }}">
                                                        <i class="bi bi-graph-up"></i>
                                                    </a>
                                                    <a href="{{ route($routePath . '.preview', $item) }}" target="_blank" class="btn-icon" title="{{ __('announcements::messages.admin.actions.preview') }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route($routePath . '.toggle-publish', $item) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn-icon" title="{{ $item->status === 'published' ? __('announcements::messages.admin.actions.unpublish') : __('announcements::messages.admin.actions.publish') }}">
                                                            <i class="bi {{ $item->status === 'published' ? 'bi-eye-slash' : 'bi-check-circle' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route($routePath . '.destroy', $item) }}" class="inline confirmation-popup">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" class="btn-icon text-red-500 hover:text-red-700" title="{{ __('global.delete') }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white dark:bg-slate-900">
                                            <td colspan="7" class="px-6 py-8 text-center">
                                                <div class="flex flex-col items-center">
                                                    <i class="bi bi-megaphone text-4xl text-gray-300 dark:text-gray-600 mb-2"></i>
                                                    <p class="text-gray-500 dark:text-gray-400">{{ __('global.no_results') }}</p>
                                                    <a href="{{ route($routePath . '.create') }}" class="btn btn-primary mt-4">
                                                        <i class="bi bi-plus mr-1"></i>
                                                        {{ __('admin.create') }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="py-1 px-4 mx-auto">
                            {{ $items->links('admin.shared.layouts.pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
