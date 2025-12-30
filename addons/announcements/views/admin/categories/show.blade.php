<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.edit_title'))
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        <form method="POST" action="{{ route($routePath . '.update', ['category' => $item]) }}">
            @csrf @method('PUT')
            <div class="card">
                <div class="card-heading">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.edit_title') }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __($translatePrefix . '.edit_subtitle') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route($routePath . '.index') }}" class="btn btn-secondary">{{ __('global.cancel') }}</a>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check mr-1"></i>{{ __('global.save') }}</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                    <div>@include('admin/shared/input', ['name' => 'name', 'label' => __('announcements::messages.categories.fields.name'), 'value' => old('name', $item->name)])</div>
                    <div>@include('admin/shared/input', ['name' => 'slug', 'label' => __('announcements::messages.categories.fields.slug'), 'value' => old('slug', $item->slug)])</div>
                </div>
                <div class="p-4 pt-0">
                    @include('admin/shared/textarea', ['name' => 'description', 'label' => __('announcements::messages.categories.fields.description'), 'value' => old('description', $item->description), 'rows' => 2])
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 pt-0">
                    <div>@include('admin/shared/input', ['name' => 'color', 'label' => __('announcements::messages.categories.fields.color'), 'value' => old('color', $item->color), 'type' => 'color'])</div>
                    <div>@include('admin/shared/input', ['name' => 'icon', 'label' => __('announcements::messages.categories.fields.icon'), 'value' => old('icon', $item->icon), 'help' => __('announcements::messages.categories.fields.icon_help')])</div>
                    <div>@include('admin/shared/input', ['name' => 'position', 'label' => __('announcements::messages.categories.fields.position'), 'value' => old('position', $item->position), 'type' => 'number', 'min' => 0])</div>
                </div>
                <div class="p-4 pt-0">
                    @include('admin/shared/checkbox', ['name' => 'is_active', 'label' => __('announcements::messages.categories.fields.is_active'), 'checked' => old('is_active', $item->is_active)])
                </div>
            </div>
        </form>
        <div class="card mt-4 border-red-200 dark:border-red-800">
            <div class="card-heading"><h3 class="text-lg font-semibold text-red-600">{{ __('announcements::messages.danger_zone') }}</h3></div>
            <div class="p-4">
                <form method="POST" action="{{ route($routePath . '.destroy', ['category' => $item]) }}" class="inline confirmation-popup">
                    @method('DELETE')@csrf
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash mr-1"></i>{{ __('global.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection
