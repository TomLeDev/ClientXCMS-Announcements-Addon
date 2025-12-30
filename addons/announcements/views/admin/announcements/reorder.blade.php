<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.reorder_title'))
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('sortable-list');
            
            Sortable.create(list, {
                animation: 150,
                handle: '.drag-handle',
                onEnd: function() {
                    const positions = {};
                    list.querySelectorAll('[data-id]').forEach((el, index) => {
                        positions[el.dataset.id] = index;
                    });
                    
                    fetch('{{ route($routePath . '.positions') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ positions: positions })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Optional: show success message
                        }
                    });
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
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.reorder_title') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.reorder_subtitle') }}</p>
                </div>
                <a href="{{ route($routePath . '.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left mr-1"></i>{{ __('global.back') }}
                </a>
            </div>
            
            <ul id="sortable-list" class="divide-y dark:divide-gray-700">
                @foreach($items as $item)
                    <li data-id="{{ $item->id }}" class="flex items-center gap-4 p-4 bg-white dark:bg-slate-900 hover:bg-gray-50 dark:hover:bg-slate-800">
                        <span class="drag-handle cursor-move text-gray-400 hover:text-gray-600">
                            <i class="bi bi-grip-vertical text-xl"></i>
                        </span>
                        
                        @if($item->cover_image_display_url)
                            <img src="{{ $item->cover_image_display_url }}" alt="" class="w-12 h-12 rounded object-cover">
                        @else
                            <div class="w-12 h-12 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                <i class="bi bi-megaphone text-gray-400"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $item->title }}</span>
                                @if($item->featured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="bi bi-star-fill mr-1"></i>Featured
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $item->status_badge_class }}">
                                    {{ $item->status_label }}
                                </span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $item->published_at?->format('d/m/Y H:i') ?? 'Not published' }}</span>
                        </div>
                        
                        <span class="text-sm text-gray-400">Position: {{ $item->position }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
