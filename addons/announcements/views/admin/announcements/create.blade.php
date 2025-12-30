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
        .settings-tabs .settings-tab-btn.active { border-bottom: 2px solid #3b82f6; color: #3b82f6; }
        .settings-tabs .settings-tab-btn { padding: 0.5rem 1rem; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
        .settings-tabs .settings-tab-btn:hover { color: #3b82f6; }
        .settings-tab-content { display: none; }
        .settings-tab-content.active { display: block; }
        .html-preview-container { border: 1px solid #374151; border-radius: 0.5rem; padding: 1rem; background: white; min-height: 200px; }
        .dark .html-preview-container { background: #1f2937; color: #e5e7eb; }
    </style>
@endsection
@section('scripts')
    <script src="{{ Vite::asset('resources/global/js/editor.js') }}" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs/loader.js"></script>
    <script>
        window.announcement = {
            html: @json(old('content_html', '')),
            theme: {!! !is_darkmode(true) ? '"vs"' : '"vs-dark"' !!}
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            // Settings tabs (Publication/Média/SEO)
            document.querySelectorAll('.settings-tab-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = this.dataset.target;
                    
                    // Update buttons
                    document.querySelectorAll('.settings-tab-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Update content
                    document.querySelectorAll('.settings-tab-content').forEach(c => c.classList.remove('active'));
                    document.querySelector(`.settings-tab-content[data-tab="${target}"]`).classList.add('active');
                });
            });
            
            // Editor mode switching
            const editorModeSelect = document.getElementById('editor_mode');
            const markdownEditorWrapper = document.getElementById('markdown-editor-wrapper');
            const htmlEditorWrapper = document.getElementById('html-editor-wrapper');
            
            function switchEditor(mode) {
                if (mode === 'markdown') {
                    markdownEditorWrapper.style.display = 'block';
                    htmlEditorWrapper.style.display = 'none';
                } else {
                    markdownEditorWrapper.style.display = 'none';
                    htmlEditorWrapper.style.display = 'block';
                    initMonacoEditor();
                }
            }
            
            if (editorModeSelect) {
                editorModeSelect.addEventListener('change', function() {
                    switchEditor(this.value);
                });
                
                // Initialize based on current mode (default markdown)
                switchEditor(editorModeSelect.value);
            }
            
            // Monaco Editor initialization
            let monacoEditor = null;
            function initMonacoEditor() {
                if (monacoEditor) return;
                
                const container = document.getElementById('monaco-editor-container');
                if (!container) return;
                
                require.config({ paths: { vs: 'https://cdn.jsdelivr.net/npm/monaco-editor@0.44.0/min/vs' } });
                require(['vs/editor/editor.main'], function () {
                    monacoEditor = monaco.editor.create(container, {
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
                        const value = monacoEditor.getValue();
                        document.getElementById('content_html_input').value = value;
                        updateHtmlPreview(value);
                    });
                    
                    // Initial preview
                    updateHtmlPreview(window.announcement.html);
                });
            }
            
            // HTML Preview
            function updateHtmlPreview(html) {
                const preview = document.getElementById('html-preview');
                if (preview) {
                    preview.innerHTML = html;
                }
            }
            
            // HTML view mode toggle (edit/preview)
            const editModeBtn = document.getElementById('html-edit-mode');
            const previewModeBtn = document.getElementById('html-preview-mode');
            const monacoContainer = document.getElementById('monaco-editor-container');
            const previewContainer = document.getElementById('html-preview-container');
            
            if (editModeBtn && previewModeBtn) {
                editModeBtn.addEventListener('click', function() {
                    editModeBtn.classList.add('btn-primary');
                    editModeBtn.classList.remove('btn-secondary');
                    previewModeBtn.classList.remove('btn-primary');
                    previewModeBtn.classList.add('btn-secondary');
                    monacoContainer.style.display = 'block';
                    previewContainer.style.display = 'none';
                });
                
                previewModeBtn.addEventListener('click', function() {
                    previewModeBtn.classList.add('btn-primary');
                    previewModeBtn.classList.remove('btn-secondary');
                    editModeBtn.classList.remove('btn-primary');
                    editModeBtn.classList.add('btn-secondary');
                    monacoContainer.style.display = 'none';
                    previewContainer.style.display = 'block';
                    updateHtmlPreview(document.getElementById('content_html_input').value);
                });
            }
        });
    </script>
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
                            'value' => old('title')
                        ])
                    </div>
                    <div>
                        @include('admin/shared/input', [
                            'name' => 'slug', 
                            'label' => __('announcements::messages.admin.fields.slug'), 
                            'value' => old('slug'),
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
                            'value' => old('status', 'draft')
                        ])
                    </div>
                    <div>
                        @include('admin/shared/select', [
                            'name' => 'category_id', 
                            'label' => __('announcements::messages.admin.fields.category'), 
                            'options' => ['' => __('global.none')] + $categories->toArray(),
                            'value' => old('category_id')
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
                            'value' => old('editor_mode', 'markdown')
                        ])
                    </div>
                </div>
                
                <div class="p-4 pt-0">
                    @include('admin/shared/textarea', [
                        'name' => 'excerpt', 
                        'label' => __('announcements::messages.admin.fields.excerpt'), 
                        'value' => old('excerpt'),
                        'rows' => 2,
                        'help' => __('announcements::messages.admin.fields.excerpt_help')
                    ])
                </div>
                
                {{-- Content editors --}}
                <div class="p-4 pt-0">
                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-400 mb-2">
                        {{ __('announcements::messages.admin.fields.content') }}
                    </label>
                    
                    {{-- Markdown editor (WYSIWYG) --}}
                    <div id="markdown-editor-wrapper">
                        @include('admin/shared/editor', [
                            'name' => 'content_markdown', 
                            'value' => old('content_markdown')
                        ])
                    </div>
                    
                    {{-- HTML/Monaco editor --}}
                    <div id="html-editor-wrapper" style="display: none;">
                        {{-- Toggle buttons --}}
                        <div class="flex gap-2 mb-2">
                            <button type="button" id="html-edit-mode" class="btn btn-sm btn-primary">
                                <i class="bi bi-code mr-1"></i>
                                {{ __('announcements::messages.admin.editor_modes.html') }}
                            </button>
                            <button type="button" id="html-preview-mode" class="btn btn-sm btn-secondary">
                                <i class="bi bi-eye mr-1"></i>
                                {{ __('announcements::messages.admin.actions.preview') }}
                            </button>
                        </div>
                        
                        {{-- Monaco editor --}}
                        <div id="monaco-editor-container" class="border rounded-lg dark:border-gray-700" style="height: 400px;"></div>
                        
                        {{-- Preview container --}}
                        <div id="html-preview-container" class="html-preview-container" style="display: none; min-height: 400px;">
                            <div id="html-preview"></div>
                        </div>
                        
                        <input type="hidden" name="content_html" id="content_html_input" value="{{ old('content_html') }}">
                    </div>
                </div>
            </div>
            
            {{-- Tabs for additional settings --}}
            <div class="card mt-4">
                <div class="settings-tabs flex border-b dark:border-gray-700">
                    <button type="button" class="settings-tab-btn active" data-target="publication">
                        <i class="bi bi-calendar mr-1"></i>
                        {{ __('announcements::messages.settings.sections.publication') }}
                    </button>
                    <button type="button" class="settings-tab-btn" data-target="media">
                        <i class="bi bi-image mr-1"></i>
                        {{ __('announcements::messages.media') }}
                    </button>
                    <button type="button" class="settings-tab-btn" data-target="seo">
                        <i class="bi bi-search mr-1"></i>
                        {{ __('announcements::messages.admin.seo.title') }}
                    </button>
                </div>
                
                {{-- Publication tab --}}
                <div class="settings-tab-content active p-4" data-tab="publication">
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
                <div class="settings-tab-content p-4" data-tab="media">
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
                <div class="settings-tab-content p-4" data-tab="seo">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            @include('admin/shared/input', [
                                'name' => 'meta_title', 
                                'label' => __('announcements::messages.admin.seo.meta_title'), 
                                'value' => old('meta_title'),
                                'help' => __('announcements::messages.admin.seo.meta_title_help')
                            ])
                        </div>
                        <div>
                            @include('admin/shared/input', [
                                'name' => 'meta_keywords', 
                                'label' => __('announcements::messages.admin.seo.meta_keywords'), 
                                'value' => old('meta_keywords'),
                                'help' => __('announcements::messages.admin.seo.meta_keywords_help')
                            ])
                        </div>
                    </div>
                    <div class="mt-4">
                        @include('admin/shared/textarea', [
                            'name' => 'meta_description', 
                            'label' => __('announcements::messages.admin.seo.meta_description'), 
                            'value' => old('meta_description'),
                            'rows' => 2,
                            'help' => __('announcements::messages.admin.seo.meta_description_help')
                        ])
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            @include('admin/shared/input', [
                                'name' => 'canonical_url', 
                                'label' => __('announcements::messages.admin.seo.canonical_url'), 
                                'value' => old('canonical_url')
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
                                'value' => old('robots', 'index,follow')
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
