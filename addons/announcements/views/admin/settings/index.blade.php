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
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Settings form --}}
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('admin.announcements-settings.update') }}">
                    @csrf
                    
                    {{-- General --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.sections.general') }}</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            @include('admin/shared/checkbox', ['name' => 'announcements_enabled', 'label' => __($translatePrefix . '.fields.enabled'), 'checked' => setting('announcements_enabled', true), 'help' => __($translatePrefix . '.fields.enabled_help')])
                            <div>
                                @include('admin/shared/input', ['name' => 'announcements_public_url', 'label' => __($translatePrefix . '.fields.public_url'), 'value' => setting('announcements_public_url', 'announcements'), 'help' => __($translatePrefix . '.fields.public_url_help')])
                            </div>
                        </div>
                    </div>
                    
                    {{-- Display --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.sections.display') }}</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            @include('admin/shared/checkbox', ['name' => 'announcements_show_views', 'label' => __($translatePrefix . '.fields.show_views'), 'checked' => setting('announcements_show_views', true)])
                            @include('admin/shared/checkbox', ['name' => 'announcements_show_author', 'label' => __($translatePrefix . '.fields.show_author'), 'checked' => setting('announcements_show_author', true)])
                            @include('admin/shared/checkbox', ['name' => 'announcements_show_date', 'label' => __($translatePrefix . '.fields.show_date'), 'checked' => setting('announcements_show_date', true)])
                            @include('admin/shared/checkbox', ['name' => 'announcements_show_featured', 'label' => __($translatePrefix . '.fields.show_featured'), 'checked' => setting('announcements_show_featured', true)])
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>@include('admin/shared/input', ['name' => 'announcements_per_page', 'label' => __($translatePrefix . '.fields.per_page'), 'value' => setting('announcements_per_page', 12), 'type' => 'number', 'min' => 1, 'max' => 100])</div>
                                <div>@include('admin/shared/select', ['name' => 'announcements_default_order', 'label' => __($translatePrefix . '.fields.default_order'), 'options' => __($translatePrefix . '.fields.default_orders'), 'value' => setting('announcements_default_order', 'featured_position_date')])</div>
                            </div>
                            <div>@include('admin/shared/input', ['name' => 'announcements_anonymous_name', 'label' => __($translatePrefix . '.fields.anonymous_name'), 'value' => setting('announcements_anonymous_name', 'Staff')])</div>
                        </div>
                    </div>
                    
                    {{-- Likes --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.sections.likes') }}</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            @include('admin/shared/checkbox', ['name' => 'announcements_likes_enabled', 'label' => __($translatePrefix . '.fields.likes_enabled'), 'checked' => setting('announcements_likes_enabled', true), 'help' => __($translatePrefix . '.fields.likes_enabled_help')])
                            <div>
                                @include('admin/shared/select', [
                                    'name' => 'announcements_likes_mode', 
                                    'label' => __($translatePrefix . '.fields.likes_mode'), 
                                    'options' => [
                                        'all' => __($translatePrefix . '.fields.likes_modes.all'),
                                        'authenticated' => __($translatePrefix . '.fields.likes_modes.authenticated'),
                                        'ip' => __($translatePrefix . '.fields.likes_modes.ip'),
                                    ], 
                                    'value' => setting('announcements_likes_mode', 'all'),
                                    'help' => __($translatePrefix . '.fields.likes_mode_help')
                                ])
                            </div>
                        </div>
                    </div>
                    
                    {{-- Tracking --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.sections.tracking') }}</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>@include('admin/shared/select', ['name' => 'announcements_view_mode', 'label' => __($translatePrefix . '.fields.view_mode'), 'options' => __($translatePrefix . '.fields.view_modes'), 'value' => setting('announcements_view_mode', 'unique')])</div>
                                <div>@include('admin/shared/input', ['name' => 'announcements_view_window', 'label' => __($translatePrefix . '.fields.view_window'), 'value' => setting('announcements_view_window', 30), 'type' => 'number', 'min' => 1, 'max' => 1440, 'help' => __($translatePrefix . '.fields.view_window_help')])</div>
                            </div>
                            @include('admin/shared/checkbox', ['name' => 'announcements_scheduling_enabled', 'label' => __($translatePrefix . '.fields.scheduling_enabled'), 'checked' => setting('announcements_scheduling_enabled', true)])
                        </div>
                    </div>
                    
                    {{-- SEO --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.sections.seo') }}</h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>@include('admin/shared/input', ['name' => 'announcements_seo_title_template', 'label' => __($translatePrefix . '.fields.seo_title_template'), 'value' => setting('announcements_seo_title_template', '{title} - {site_name}'), 'help' => __($translatePrefix . '.fields.seo_title_template_help')])</div>
                            <div>@include('admin/shared/input', ['name' => 'announcements_default_meta_description', 'label' => __($translatePrefix . '.fields.default_meta_description'), 'value' => setting('announcements_default_meta_description')])</div>
                            <div>@include('admin/shared/input', ['name' => 'announcements_default_og_image', 'label' => __($translatePrefix . '.fields.default_og_image'), 'value' => setting('announcements_default_og_image')])</div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check mr-1"></i>{{ __('global.save') }}</button>
                </form>
            </div>
            
            {{-- Stats sidebar --}}
            <div>
                <div class="card sticky top-4">
                    <div class="card-heading">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.global_stats.title') }}</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.global_stats.total_announcements') }}</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $globalStats['total_announcements'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.global_stats.published_announcements') }}</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $globalStats['published_announcements'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.global_stats.total_views') }}</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($globalStats['total_views']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.global_stats.total_likes') }}</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ number_format($globalStats['total_likes']) }}</span>
                        </div>
                        
                        @if($globalStats['most_viewed']->count() > 0)
                            <hr class="dark:border-gray-700">
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __($translatePrefix . '.global_stats.most_viewed') }}</h4>
                                <ul class="space-y-1">
                                    @foreach($globalStats['most_viewed'] as $ann)
                                        <li class="flex justify-between text-sm">
                                            <a href="{{ route('admin.announcements.show', $ann) }}" class="text-blue-600 hover:underline truncate max-w-[150px]">{{ $ann->title }}</a>
                                            <span class="text-gray-500">{{ number_format($ann->views_count) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
