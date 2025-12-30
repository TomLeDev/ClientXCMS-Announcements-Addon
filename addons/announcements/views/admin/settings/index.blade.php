<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('admin/layouts/admin')
@section('title', __($translatePrefix . '.title'))
@section('styles')
<style>
    .discord-preview {
        background: #36393f;
        border-radius: 8px;
        padding: 16px;
        font-family: 'gg sans', 'Noto Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
    }
    .discord-embed {
        border-left: 4px solid #3b82f6;
        background: #2f3136;
        border-radius: 4px;
        padding: 12px;
        max-width: 520px;
    }
    .discord-embed-author {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .discord-embed-author img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
    }
    .discord-embed-author-name {
        font-size: 14px;
        font-weight: 500;
        color: #fff;
    }
    .discord-embed-title {
        font-size: 16px;
        font-weight: 600;
        color: #00aff4;
        margin-bottom: 8px;
    }
    .discord-embed-description {
        font-size: 14px;
        color: #dcddde;
        margin-bottom: 12px;
        line-height: 1.4;
    }
    .discord-embed-fields {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 8px;
        margin-bottom: 12px;
    }
    .discord-embed-field {
        min-width: 0;
    }
    .discord-embed-field-name {
        font-size: 14px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 2px;
    }
    .discord-embed-field-value {
        font-size: 14px;
        color: #dcddde;
    }
    .discord-embed-image {
        border-radius: 4px;
        max-width: 100%;
        margin-top: 12px;
    }
    .discord-embed-thumbnail {
        float: right;
        width: 80px;
        height: 80px;
        border-radius: 4px;
        margin-left: 16px;
        object-fit: cover;
    }
    .discord-embed-footer {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        font-size: 12px;
        color: #72767d;
    }
    .discord-embed-footer img {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }
    .discord-content {
        color: #dcddde;
        font-size: 16px;
        margin-bottom: 8px;
    }
    .variable-tag {
        display: inline-block;
        background: #3b82f6;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin: 2px;
        cursor: pointer;
        transition: background 0.2s;
    }
    .variable-tag:hover {
        background: #2563eb;
    }
</style>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy variable to clipboard or insert into focused field
    document.querySelectorAll('.variable-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            const variable = this.dataset.variable;
            const activeElement = document.activeElement;
            
            if (activeElement && (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA')) {
                const start = activeElement.selectionStart;
                const end = activeElement.selectionEnd;
                const value = activeElement.value;
                activeElement.value = value.substring(0, start) + variable + value.substring(end);
                activeElement.selectionStart = activeElement.selectionEnd = start + variable.length;
                activeElement.focus();
                updatePreview();
            } else {
                navigator.clipboard.writeText(variable);
            }
        });
    });
    
    // Update preview on input change
    const previewInputs = document.querySelectorAll('[data-preview-update]');
    previewInputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // Initial preview
    updatePreview();
    
    function updatePreview() {
        const title = document.getElementById('announcements_discord_embed_title')?.value || '{title}';
        const description = document.getElementById('announcements_discord_embed_description')?.value || '{excerpt}';
        const color = document.getElementById('announcements_discord_embed_color')?.value || '#3b82f6';
        const content = document.getElementById('announcements_discord_content')?.value || '';
        const footer = document.getElementById('announcements_discord_embed_footer')?.value || '';
        const showAuthor = document.getElementById('announcements_discord_embed_author')?.checked || false;
        const showImage = document.getElementById('announcements_discord_embed_image')?.checked || false;
        const showTimestamp = document.getElementById('announcements_discord_embed_timestamp')?.checked || false;
        const showCategory = document.getElementById('announcements_discord_embed_field_category')?.checked || false;
        
        // Replace variables with example values for preview
        const variables = {
            '{title}': 'Exemple d\'annonce',
            '{slug}': 'exemple-annonce',
            '{excerpt}': 'Ceci est un exemple d\'extrait pour la prévisualisation de l\'embed Discord.',
            '{url}': window.location.origin + '/announcements/exemple',
            '{author}': 'Admin',
            '{category}': 'Actualités',
            '{status}': 'Publié',
            '{published_at}': new Date().toLocaleDateString('fr-FR'),
            '{views}': '123',
            '{likes}': '45',
            '{site_name}': '{{ setting("app_name", "ClientXCMS") }}',
            '{site_url}': window.location.origin,
        };
        
        function replaceVars(text) {
            for (const [key, value] of Object.entries(variables)) {
                text = text.replace(new RegExp(key.replace(/[{}]/g, '\\$&'), 'g'), value);
            }
            return text;
        }
        
        // Update preview elements
        const previewEmbed = document.getElementById('discord-preview-embed');
        if (previewEmbed) {
            previewEmbed.style.borderLeftColor = color;
        }
        
        const previewTitle = document.getElementById('discord-preview-title');
        if (previewTitle) {
            previewTitle.textContent = replaceVars(title);
        }
        
        const previewDesc = document.getElementById('discord-preview-description');
        if (previewDesc) {
            previewDesc.textContent = replaceVars(description);
        }
        
        const previewContent = document.getElementById('discord-preview-content');
        if (previewContent) {
            previewContent.textContent = replaceVars(content);
            previewContent.style.display = content ? 'block' : 'none';
        }
        
        const previewFooter = document.getElementById('discord-preview-footer');
        if (previewFooter) {
            previewFooter.textContent = replaceVars(footer);
            previewFooter.parentElement.style.display = footer ? 'flex' : 'none';
        }
        
        const previewAuthor = document.getElementById('discord-preview-author');
        if (previewAuthor) {
            previewAuthor.style.display = showAuthor ? 'flex' : 'none';
        }
        
        const previewImage = document.getElementById('discord-preview-image');
        if (previewImage) {
            previewImage.style.display = showImage ? 'block' : 'none';
        }
        
        const previewTimestamp = document.getElementById('discord-preview-timestamp');
        if (previewTimestamp) {
            previewTimestamp.style.display = showTimestamp ? 'inline' : 'none';
        }
        
        const previewCategory = document.getElementById('discord-preview-category');
        if (previewCategory) {
            previewCategory.style.display = showCategory ? 'block' : 'none';
        }
    }
    
    // Test webhook button
    const testBtn = document.getElementById('test-webhook-btn');
    if (testBtn) {
        testBtn.addEventListener('click', function() {
            const url = document.getElementById('announcements_discord_webhook_url')?.value;
            if (!url) {
                alert('{{ __("announcements::messages.settings.discord.webhook_required") }}');
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split mr-1"></i>{{ __("announcements::messages.settings.discord.testing") }}';
            
            fetch('{{ route("admin.announcements-settings.test-discord") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ webhook_url: url })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ __("announcements::messages.settings.discord.test_success") }}');
                } else {
                    alert('{{ __("announcements::messages.settings.discord.test_failed") }}: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('{{ __("announcements::messages.settings.discord.test_failed") }}: ' + error.message);
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-send mr-1"></i>{{ __("announcements::messages.settings.discord.test_webhook") }}';
            });
        });
    }
});
</script>
@endsection
@section('content')
    <div class="container mx-auto">
        @include('admin/shared/alerts')
        
        <form method="POST" action="{{ route('admin.announcements-settings.update') }}">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Settings form --}}
                <div class="lg:col-span-2">
                    {{-- General --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.sections.general') }}</h3>
                        </div>
                        <div class="p-4 space-y-4">
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
                    
                    {{-- Discord Webhook --}}
                    <div class="card mb-4">
                        <div class="card-heading">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                <i class="bi bi-discord mr-2"></i>{{ __($translatePrefix . '.discord.title') }}
                            </h3>
                        </div>
                        <div class="p-4 space-y-4">
                            @include('admin/shared/checkbox', [
                                'name' => 'announcements_discord_enabled', 
                                'label' => __($translatePrefix . '.discord.enabled'), 
                                'checked' => setting('announcements_discord_enabled', false),
                                'help' => __($translatePrefix . '.discord.enabled_help')
                            ])
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    @include('admin/shared/input', [
                                        'name' => 'announcements_discord_webhook_url', 
                                        'label' => __($translatePrefix . '.discord.webhook_url'), 
                                        'value' => setting('announcements_discord_webhook_url'),
                                        'type' => 'url',
                                        'help' => __($translatePrefix . '.discord.webhook_url_help')
                                    ])
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="test-webhook-btn" class="btn btn-secondary">
                                        <i class="bi bi-send mr-1"></i>{{ __($translatePrefix . '.discord.test_webhook') }}
                                    </button>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>@include('admin/shared/input', ['name' => 'announcements_discord_username', 'label' => __($translatePrefix . '.discord.username'), 'value' => setting('announcements_discord_username'), 'help' => __($translatePrefix . '.discord.username_help')])</div>
                                <div>@include('admin/shared/input', ['name' => 'announcements_discord_avatar_url', 'label' => __($translatePrefix . '.discord.avatar_url'), 'value' => setting('announcements_discord_avatar_url')])</div>
                            </div>
                            
                            <div>
                                @include('admin/shared/textarea', [
                                    'name' => 'announcements_discord_content', 
                                    'label' => __($translatePrefix . '.discord.content'), 
                                    'value' => setting('announcements_discord_content'),
                                    'rows' => 2,
                                    'help' => __($translatePrefix . '.discord.content_help'),
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_content']
                                ])
                            </div>
                            
                            {{-- Variables --}}
                            <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __($translatePrefix . '.discord.available_variables') }}</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(['{title}', '{slug}', '{excerpt}', '{url}', '{author}', '{category}', '{status}', '{published_at}', '{views}', '{likes}', '{site_name}', '{site_url}'] as $var)
                                        <span class="variable-tag" data-variable="{{ $var }}">{{ $var }}</span>
                                    @endforeach
                                </div>
                            </div>
                            
                            <hr class="dark:border-gray-700">
                            
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200">{{ __($translatePrefix . '.discord.embed_settings') }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    @include('admin/shared/input', [
                                        'name' => 'announcements_discord_embed_title', 
                                        'label' => __($translatePrefix . '.discord.embed_title'), 
                                        'value' => setting('announcements_discord_embed_title', '{title}'),
                                        'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_title']
                                    ])
                                </div>
                                <div>
                                    @include('admin/shared/input', [
                                        'name' => 'announcements_discord_embed_color', 
                                        'label' => __($translatePrefix . '.discord.embed_color'), 
                                        'value' => setting('announcements_discord_embed_color', '#3b82f6'),
                                        'type' => 'color',
                                        'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_color']
                                    ])
                                </div>
                            </div>
                            
                            <div>
                                @include('admin/shared/textarea', [
                                    'name' => 'announcements_discord_embed_description', 
                                    'label' => __($translatePrefix . '.discord.embed_description'), 
                                    'value' => setting('announcements_discord_embed_description', '{excerpt}'),
                                    'rows' => 2,
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_description']
                                ])
                            </div>
                            
                            <div>
                                @include('admin/shared/input', [
                                    'name' => 'announcements_discord_embed_footer', 
                                    'label' => __($translatePrefix . '.discord.embed_footer'), 
                                    'value' => setting('announcements_discord_embed_footer'),
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_footer']
                                ])
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @include('admin/shared/checkbox', [
                                    'name' => 'announcements_discord_embed_timestamp', 
                                    'label' => __($translatePrefix . '.discord.embed_timestamp'), 
                                    'checked' => setting('announcements_discord_embed_timestamp', true),
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_timestamp']
                                ])
                                @include('admin/shared/checkbox', [
                                    'name' => 'announcements_discord_embed_image', 
                                    'label' => __($translatePrefix . '.discord.embed_image'), 
                                    'checked' => setting('announcements_discord_embed_image', true),
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_image']
                                ])
                                @include('admin/shared/checkbox', [
                                    'name' => 'announcements_discord_embed_author', 
                                    'label' => __($translatePrefix . '.discord.embed_author'), 
                                    'checked' => setting('announcements_discord_embed_author', false),
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_author']
                                ])
                                @include('admin/shared/checkbox', [
                                    'name' => 'announcements_discord_embed_field_category', 
                                    'label' => __($translatePrefix . '.discord.embed_field_category'), 
                                    'checked' => setting('announcements_discord_embed_field_category', true),
                                    'attributes' => ['data-preview-update' => 'true', 'id' => 'announcements_discord_embed_field_category']
                                ])
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>@include('admin/shared/input', ['name' => 'announcements_discord_embed_thumbnail', 'label' => __($translatePrefix . '.discord.embed_thumbnail'), 'value' => setting('announcements_discord_embed_thumbnail')])</div>
                                <div>@include('admin/shared/input', ['name' => 'announcements_discord_embed_footer_icon', 'label' => __($translatePrefix . '.discord.embed_footer_icon'), 'value' => setting('announcements_discord_embed_footer_icon')])</div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check mr-1"></i>{{ __('global.save') }}</button>
                </form>
            </div>
            
            {{-- Sidebar --}}
            <div>
                {{-- Discord Preview --}}
                <div class="card sticky top-4 mb-4">
                    <div class="card-heading">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            <i class="bi bi-eye mr-2"></i>{{ __($translatePrefix . '.discord.preview') }}
                        </h3>
                    </div>
                    <div class="p-4">
                        <div class="discord-preview">
                            <div id="discord-preview-content" class="discord-content" style="display: none;"></div>
                            <div id="discord-preview-embed" class="discord-embed">
                                <div id="discord-preview-author" class="discord-embed-author" style="display: none;">
                                    <span class="discord-embed-author-name">Admin</span>
                                </div>
                                <div id="discord-preview-title" class="discord-embed-title">Exemple d'annonce</div>
                                <div id="discord-preview-description" class="discord-embed-description">Ceci est un exemple d'extrait pour la prévisualisation.</div>
                                <div id="discord-preview-category" class="discord-embed-fields">
                                    <div class="discord-embed-field">
                                        <div class="discord-embed-field-name">{{ __('announcements::messages.admin.fields.category') }}</div>
                                        <div class="discord-embed-field-value">Actualités</div>
                                    </div>
                                </div>
                                <img id="discord-preview-image" class="discord-embed-image" src="https://via.placeholder.com/400x200/3b82f6/ffffff?text=Image+de+couverture" alt="Preview">
                                <div class="discord-embed-footer">
                                    <span id="discord-preview-footer"></span>
                                    <span id="discord-preview-timestamp">{{ now()->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Stats sidebar --}}
                <div class="card">
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
