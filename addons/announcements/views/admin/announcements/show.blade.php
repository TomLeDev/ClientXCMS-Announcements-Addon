<?php
/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.edit_title'))
@section('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/global/css/editor.scss') }}">
    <link rel="stylesheet" href="{{ Vite::asset('resources/global/css/monaco-editor.main.css') }}">
    <style>
        .editor-tabs .tab-btn.active { border-bottom: 2px solid #3b82f6; color: #3b82f6; }
        .editor-tabs .tab-btn { padding: 0.5rem 1rem; cursor: pointer; border-bottom: 2px solid transparent; }
        .editor-content { display: none; }
        .editor-content.active { display: block; }
    </style>
@endsection
@section('scripts')
    <script src="{{ Vite::asset('resources/global/js/editor.js') }}" type="module"></script>
    <script>
        window.announcement = {
            html: @json(old('content_html', $item->content_html ?? '')),
            theme: {!! !is_darkmode(true) ? '"vs"' : '"vs-dark"' !!}
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            // Editor mode switching
            const editorModeSelect = document.getElementById('editor_mode');
            const markdownEditor = document.getElementById('markdown-editor');
            const htmlEditor = document.getElementById('html-editor');
            
            function switchEditor(mode) {
                if (mode === 'markdown') {
                    markdownEditor.classList.add('active');
                    htmlEditor.classList.remove('active');
                } else {
                    markdownEditor.classList.remove('active');
                    htmlEditor.classList.add('active');
                    initMonacoEditor();
                }
            }
            
            editorModeSelect.addEventListener('change', function() {
                switchEditor(this.value);
            });
            
            // Initialize based on current mode
            switchEditor(editorModeSelect.value);
            
            // Monaco Editor initialization
            let monacoEditor = null;
            function initMonacoEditor() {
                if (monacoEditor) return;
                
                require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs' } });
                require(['vs/editor/editor.main'], function () {
                    monacoEditor = monaco.editor.create(document.getElementById('monaco-editor-container'), {
                        value: window.announcement.html,
                        language: 'html',
                        theme: window.announcement.theme,
                        automaticLayout: true,
                        minimap: { enabled: false },
                        lineNumbers: 'on',
                        scrollBeyondLastLine: false,
                        wordWrap: 'on'
                    });
                    
                    monacoEditor.onDidChangeModelContent(function() {
                        document.getElementById('content_html_input').value = monacoEditor.getValue();
                    });
                });
            }
            
            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const tabGroup = this.closest('.editor-tabs');
                    const contentGroup = tabGroup.nextElementSibling;
                    
                    tabGroup.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    contentGroup.querySelectorAll('.editor-content').forEach(c => c.classList.remove('active'));
                    contentGroup.querySelector(`[data-tab="${this.dataset.target}"]`).classList.add('active');
                });
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs/loader.js"></script>
@endsection
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        
        <form method="POST" action="{{ route($routePath . '.update', $item) }}" enctype="multipart/form-data" id="announcement-form">
            @csrf
            @method('PUT')
            
            <div class="card">
                <div class="card-heading">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            {{ __($translatePrefix . '.edit_title') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __($translatePrefix . '.edit_subtitle') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route($routePath . '.preview', $item) }}" target="_blank" class="btn btn-secondary">
                            <i class="bi bi-eye mr-1"></i>
                            {{ __('announcements::messages.admin.actions.preview') }}
                        </a>
                        <button type="button" onclick="document.getElementById('duplicate-form').submit();" class="btn btn-secondary">
                            <i class="bi bi-copy mr-1"></i>
                            {{ __('announcements::messages.admin.actions.duplicate') }}
                        </button>
                        <a href="{{ route($routePath . '.stats', $item) }}" class="btn btn-secondary">
                            <i class="bi bi-graph-up mr-1"></i>
                            {{ __('announcements::messages.admin.actions.stats') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check mr-1"></i>
                            {{ __('global.save') }}
                        </button>
                    </div>
                </div>
                
                {{-- Quick stats --}}
                <div class="grid grid-cols-4 gap-4 p-4 bg-gray-50 dark:bg-gray-800 border-b dark:border-gray-700">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($item->views_count) }}</div>
                        <div class="text-sm text-gray-500">{{ __('announcements::messages.admin.fields.views') }}</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($item->likes_count) }}</div>
                        <div class="text-sm text-gray-500">{{ __('announcements::messages.admin.fields.likes') }}</div>
                    </div>
                    <div class="text-center">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $item->status_badge_class }}">
                            {{ $item->status_label }}
                        </span>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-500">{{ __('announcements::messages.admin.fields.author') }}</div>
                        <div class="text-gray-800 dark:text-gray-200">{{ $item->author?->username ?? __('announcements::messages.staff') }}</div>
                    </div>
                </div>
                
                {{-- Basic info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                    <div>
                        @include('admin/shared/input', [
                            'name' => 'title', 
                            'label' => __('announcements::messages.admin.fields.title'), 
                            'value' => old('title', $item->title)
                        ])
                    </div>
                    <div>
                        @include('admin/shared/input', [
                            'name' => 'slug', 
                            'label' => __('announcements::messages.admin.fields.slug'), 
                            'value' => old('slug', $item->slug)
                        ])
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 pt-0">
                    <div>
                        @include('admin/shared/select', [
                            'name' => 'status', 
                            'label' => __('global.status'), 
                            'options' => $statuses,
                            'value' => old('status', $item->status)
                        ])
                    </div>
                    <div>
                        @include('admin/shared/select', [
                            'name' => 'category_id', 
                            'label' => __('announcements::messages.admin.fields.category'), 
                            'options' => ['' => __('global.none')] + $categories->toArray(),
                            'value' => old('category_id', $item->category_id)
                        ])
                    </div>
                    <div>
                        @include('admin/shared/select', [
                            'name' => 'editor_mode', 
                            'label' => __('announcements::messages.admin.fields.editor_mode'), 
                            'options' => [
                                'markdown' => __('announcements::messages.admin.editor_modes.markdown'),
                                'html' => __('announcements::messages.admin.editor_modes.html'),
                            ],
                            'value' => old('editor_mode', $item->editor_mode)
                        ])
                    </div>
                </div>
                
                <div class="p-4 pt-0">
                    @include('admin/shared/textarea', [
                        'name' => 'excerpt', 
                        'label' => __('announcements::messages.admin.fields.excerpt'), 
                        'value' => old('excerpt', $item->excerpt),
                        'rows' => 2,
                        'help' => __('announcements::messages.admin.fields.excerpt_help')
                    ])
                </div>
                
                {{-- Content editors --}}
                <div class="p-4 pt-0">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-2">
                        {{ __('announcements::messages.admin.fields.content') }}
                    </label>
                    
                    {{-- Markdown editor --}}
                    <div id="markdown-editor" class="editor-content {{ $item->editor_mode === 'markdown' ? 'active' : '' }}">
                        @include('admin/shared/editor', [
                            'name' => 'content_markdown', 
                            'value' => old('content_markdown', $item->content_markdown)
                        ])
                    </div>
                    
                    {{-- HTML/Monaco editor --}}
                    <div id="html-editor" class="editor-content {{ $item->editor_mode === 'html' ? 'active' : '' }}">
                        <div id="monaco-editor-container" class="border rounded-lg dark:border-gray-700" style="height: 400px;"></div>
                        <input type="hidden" name="content_html" id="content_html_input" value="{{ old('content_html', $item->content_html) }}">
                    </div>
                </div>
            </div>
            
            {{-- Tabs for additional settings --}}
            <div class="card mt-4">
                <div class="editor-tabs flex border-b dark:border-gray-700">
                    <button type="button" class="tab-btn active" data-target="publication">
                        <i class="bi bi-calendar mr-1"></i>
                        {{ __('announcements::messages.settings.sections.publication') }}
                    </button>
                    <button type="button" class="tab-btn" data-target="media">
                        <i class="bi bi-image mr-1"></i>
                        {{ __('announcements::messages.media') }}
                    </button>
                    <button type="button" class="tab-btn" data-target="seo">
                        <i class="bi bi-search mr-1"></i>
                        {{ __('announcements::messages.admin.seo.title') }}
                    </button>
                </div>
                
                <div class="tab-contents">
                    {{-- Publication tab --}}
                    <div class="editor-content active p-4" data-tab="publication">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="published_at" class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-2">
                                    {{ __('announcements::messages.admin.fields.published_at') }}
                                </label>
                                <input type="datetime-local" 
                                    name="published_at" 
                                    id="published_at"
                                    value="{{ old('published_at', $item->published_at?->format('Y-m-d\TH:i')) }}"
                                    class="input-text">
                            </div>
                            <div>
                                @include('admin/shared/input', [
                                    'name' => 'position', 
                                    'label' => __('announcements::messages.admin.fields.position'), 
                                    'value' => old('position', $item->position),
                                    'type' => 'number',
                                    'min' => 0
                                ])
                            </div>
                            <div class="flex items-end gap-4">
                                @include('admin/shared/checkbox', [
                                    'name' => 'featured', 
                                    'label' => __('announcements::messages.admin.fields.featured'), 
                                    'checked' => old('featured', $item->featured),
                                    'help' => __('announcements::messages.admin.fields.featured_help')
                                ])
                            </div>
                        </div>
                        <div class="mt-4">
                            @include('admin/shared/checkbox', [
                                'name' => 'show_author', 
                                'label' => __('announcements::messages.admin.fields.show_author'), 
                                'checked' => old('show_author', $item->show_author)
                            ])
                        </div>
                    </div>
                    
                    {{-- Media tab --}}
                    <div class="editor-content p-4" data-tab="media">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-2">
                                    {{ __('announcements::messages.admin.fields.cover_image') }}
                                </label>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">
                                    {{ __('announcements::messages.admin.fields.cover_image_help') }}
                                </p>
                                @if($item->cover_image_display_url)
                                    <div class="mb-4">
                                        <img src="{{ $item->cover_image_display_url }}" alt="" class="max-w-xs rounded-lg shadow">
                                    </div>
                                @endif
                                @include('admin/shared/file', [
                                    'name' => 'cover_image', 
                                    'extensions' => 'jpg,jpeg,png,gif,webp',
                                    'canRemove' => $item->cover_image ? true : false
                                ])
                                <div class="mt-4 pt-4 border-t dark:border-gray-700">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                        {{ __('announcements::messages.admin.fields.cover_image_url') }}
                                    </label>
                                    <input type="text" name="cover_image_url" value="{{ old('cover_image_url', $item->cover_image_url) }}" 
                                        class="input-text" placeholder="https://example.com/image.jpg">
                                    <p class="text-xs text-gray-500 mt-1">{{ __('announcements::messages.admin.fields.cover_image_url_help') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- SEO tab --}}
                    <div class="editor-content p-4" data-tab="seo">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                @include('admin/shared/input', [
                                    'name' => 'meta_title', 
                                    'label' => __('announcements::messages.admin.seo.meta_title'), 
                                    'value' => old('meta_title', $item->meta_title),
                                    'help' => __('announcements::messages.admin.seo.meta_title_help')
                                ])
                            </div>
                            <div>
                                @include('admin/shared/input', [
                                    'name' => 'meta_keywords', 
                                    'label' => __('announcements::messages.admin.seo.meta_keywords'), 
                                    'value' => old('meta_keywords', $item->meta_keywords),
                                    'help' => __('announcements::messages.admin.seo.meta_keywords_help')
                                ])
                            </div>
                        </div>
                        <div class="mt-4">
                            @include('admin/shared/textarea', [
                                'name' => 'meta_description', 
                                'label' => __('announcements::messages.admin.seo.meta_description'), 
                                'value' => old('meta_description', $item->meta_description),
                                'rows' => 2,
                                'help' => __('announcements::messages.admin.seo.meta_description_help')
                            ])
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                @include('admin/shared/input', [
                                    'name' => 'canonical_url', 
                                    'label' => __('announcements::messages.admin.seo.canonical_url'), 
                                    'value' => old('canonical_url', $item->canonical_url)
                                ])
                            </div>
                            <div>
                                @include('admin/shared/select', [
                                    'name' => 'robots', 
                                    'label' => __('announcements::messages.admin.seo.robots'), 
                                    'options' => [
                                        'index,follow' => 'index, follow',
                                        'noindex,follow' => 'noindex, follow',
                                        'index,nofollow' => 'index, nofollow',
                                        'noindex,nofollow' => 'noindex, nofollow',
                                    ],
                                    'value' => old('robots', $item->robots)
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        {{-- Duplicate form (outside main form to avoid nesting) --}}
        <form id="duplicate-form" method="POST" action="{{ route($routePath . '.duplicate', $item) }}" class="hidden">
            @csrf
        </form>
            
        {{-- Danger zone --}}
        <div class="card mt-4 border-red-200 dark:border-red-800">
            <div class="card-heading">
                <div>
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">
                        {{ __('announcements::messages.danger_zone') }}
                    </h3>
                </div>
            </div>
            <div class="p-4">
                <form method="POST" action="{{ route($routePath . '.destroy', $item) }}" class="inline confirmation-popup">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash mr-1"></i>
                        {{ __('global.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
