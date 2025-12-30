<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.create_title'))
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        <form method="POST" action="{{ route($routePath . '.store') }}">
            @csrf
            <div class="card">
                <div class="card-heading">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.create_title') }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.create_subtitle') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route($routePath . '.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check mr-1"></i>{{ __('admin.create') }}</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                    <div>@include('admin/shared/input', ['name' => 'name', 'label' => __('announcements::messages.categories.fields.name'), 'value' => old('name', $item->name)])</div>
                    <div>@include('admin/shared/input', ['name' => 'slug', 'label' => __('announcements::messages.categories.fields.slug'), 'value' => old('slug', $item->slug), 'help' => __('global.auto_generated')])</div>
                </div>
                <div class="p-4 pt-0">
                    @include('admin/shared/textarea', ['name' => 'description', 'label' => __('announcements::messages.categories.fields.description'), 'value' => old('description', $item->description), 'rows' => 2])
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 pt-0">
                    <div>@include('admin/shared/input', ['name' => 'color', 'label' => __('announcements::messages.categories.fields.color'), 'value' => old('color', $item->color ?? '#3b82f6'), 'type' => 'color'])</div>
                    <div>@include('admin/shared/input', ['name' => 'icon', 'label' => __('announcements::messages.categories.fields.icon'), 'value' => old('icon', $item->icon), 'help' => __('announcements::messages.categories.fields.icon_help')])</div>
                    <div>@include('admin/shared/input', ['name' => 'position', 'label' => __('announcements::messages.categories.fields.position'), 'value' => old('position', $item->position ?? 0), 'type' => 'number', 'min' => 0])</div>
                </div>
                <div class="p-4 pt-0">
                    @include('admin/shared/checkbox', ['name' => 'is_active', 'label' => __('announcements::messages.categories.fields.is_active'), 'checked' => old('is_active', $item->is_active ?? true)])
                </div>
            </div>
        </form>
    </div>
@endsection
