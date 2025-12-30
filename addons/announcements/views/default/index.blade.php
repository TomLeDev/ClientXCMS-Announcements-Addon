<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('layouts.front')
@section('title', __('announcements::messages.front.title'))
@section('content')
    <div class="max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">{{ __('announcements::messages.front.title') }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ __('announcements::messages.front.all_announcements') }}</p>
        </div>
        
        {{-- Search and filters --}}
        <div class="mb-8 flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                        id="search-input"
                        value="{{ $searchQuery }}"
                        class="w-full py-3 px-4 ps-11 border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400"
                        placeholder="{{ __('announcements::messages.front.search_placeholder') }}">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('announcements.index') }}" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !$currentCategory ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}">
                    {{ __('announcements::messages.front.all_categories') }}
                </a>
                @foreach($categories as $category)
                    <a href="{{ route('announcements.index', ['category' => $category->slug]) }}" 
                        class="px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2 {{ $currentCategory === $category->slug ? 'text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}"
                        style="{{ $currentCategory === $category->slug ? 'background-color: ' . $category->color : '' }}">
                        @if($category->icon)
                            <i class="{{ $category->icon }}"></i>
                        @endif
                        {{ $category->name }}
                        <span class="text-xs opacity-75">({{ $category->announcements_count }})</span>
                    </a>
                @endforeach
            </div>
        </div>
        
        {{-- Announcements grid --}}
        <div id="announcements-list">
            @include('announcements::partials.list', ['announcements' => $announcements])
        </div>
        
        {{-- Pagination --}}
        @if($announcements->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $announcements->withQueryString()->links('shared.layouts.pagination') }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        if (!searchInput) return;
        
        let debounceTimer;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                const query = this.value;
                const url = new URL(window.location.href);
                
                if (query) {
                    url.searchParams.set('q', query);
                } else {
                    url.searchParams.delete('q');
                }
                url.searchParams.delete('page');
                
                fetch(url, { 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    } 
                })
                .then(response => response.text())
                .then(html => {
                    const listContainer = document.getElementById('announcements-list');
                    if (listContainer) {
                        listContainer.innerHTML = html;
                    }
                    window.history.pushState({}, '', url);
                })
                .catch(error => console.error('Search error:', error));
            }, 300);
        });
    });
</script>
@endsection
