<?php
/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.create_title'))
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
            
            // Tab switching for SEO/Settings
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
        
        <form method="POST" action="{{ route($routePath . '.store') }}" enctype="multipart/form-data" id="announcement-form">
            @csrf
            
            <div class="card">
                <div class="card-heading">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            {{ __($translatePrefix . '.create_title') }}
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __($translatePrefix . '.create_subtitle') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route($routePath . '.index') }}" class="btn btn-secondary">
                            {{ __('global.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check mr-1"></i>
                            {{ __('admin.create') }}
                        </button>
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
                            'value' => old('slug', $item->slug),
                            'help' => __('global.auto_generated')
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
                            'value' => old('editor_mode', $item->editor_mode ?? 'markdown')
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
                    <div id="markdown-editor" class="editor-content active">
                        @include('admin/shared/editor', [
                            'name' => 'content_markdown', 
                            'value' => old('content_markdown', $item->content_markdown)
                        ])
                    </div>
                    
                    {{-- HTML/Monaco editor --}}
                    <div id="html-editor" class="editor-content">
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
                                    value="{{ old('published_at') }}"
                                    class="input-text">
                            </div>
                            <div>
                                @include('admin/shared/input', [
                                    'name' => 'position', 
                                    'label' => __('announcements::messages.admin.fields.position'), 
                                    'value' => old('position', 0),
                                    'type' => 'number',
                                    'min' => 0
                                ])
                            </div>
                            <div class="flex items-center pt-7">
                                @include('admin/shared/checkbox', [
                                    'name' => 'featured', 
                                    'label' => __('announcements::messages.admin.fields.featured'), 
                                    'checked' => old('featured', false)
                                ])
                            </div>
                        </div>
                        <div class="mt-4">
                            @include('admin/shared/checkbox', [
                                'name' => 'show_author', 
                                'label' => __('announcements::messages.admin.fields.show_author'), 
                                'checked' => old('show_author', true)
                            ])
                        </div>
                    </div>
                    
                    {{-- Media tab --}}
                    <div class="editor-content p-4" data-tab="media">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-500 mb-2">
                                {{ __('announcements::messages.admin.fields.cover_image_help') }}
                            </p>
                            @include('admin/shared/file', [
                                'name' => 'cover_image', 
                                'label' => __('announcements::messages.admin.fields.cover_image'), 
                                'extensions' => 'jpg,jpeg,png,gif,webp'
                            ])
                            <div class="mt-4 pt-4 border-t dark:border-gray-700">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">
                                    {{ __('announcements::messages.admin.fields.cover_image_url') }}
                                </label>
                                <input type="text" name="cover_image_url" value="{{ old('cover_image_url') }}" 
                                    class="input-text" placeholder="https://example.com/image.jpg">
                                <p class="text-xs text-gray-500 mt-1">{{ __('announcements::messages.admin.fields.cover_image_url_help') }}</p>
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
                                    'value' => old('robots', $item->robots ?? 'index,follow')
                                ])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
