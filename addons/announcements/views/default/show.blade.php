<?php
/*
 * This file is part of the CLIENTXCMS project.
 * Year: 2024
 */
?>
@extends('layouts.front')
@section('title', $announcement->seo_title)
@section('styles')
    <meta name="description" content="{{ $announcement->seo_description }}">
    @if($announcement->meta_keywords)
        <meta name="keywords" content="{{ $announcement->meta_keywords }}">
    @endif
    <meta property="og:title" content="{{ $announcement->seo_title }}">
    <meta property="og:description" content="{{ $announcement->seo_description }}">
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ $announcement->url }}">
    @if($announcement->og_image_display_url)
        <meta property="og:image" content="{{ $announcement->og_image_display_url }}">
    @endif
    @if($announcement->canonical_url)
        <link rel="canonical" href="{{ $announcement->canonical_url }}">
    @endif
    <meta name="robots" content="{{ $announcement->robots }}">
    <meta name="twitter:card" content="summary_large_image">
@endsection
@section('content')
    <article class="max-w-4xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        {{-- Breadcrumb --}}
        <nav class="mb-6">
            <ol class="flex items-center gap-2 text-sm text-gray-500">
                <li><a href="{{ route('announcements.index') }}" class="hover:text-blue-600">{{ __('announcements::messages.front.title') }}</a></li>
                <li>/</li>
                @if($announcement->category)
                    <li><a href="{{ route('announcements.index', ['category' => $announcement->category->slug]) }}" class="hover:text-blue-600">{{ $announcement->category->name }}</a></li>
                    <li>/</li>
                @endif
                <li class="text-gray-800 dark:text-gray-200">{{ $announcement->title }}</li>
            </ol>
        </nav>
        
        {{-- Header --}}
        <header class="mb-8">
            @if($announcement->category)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium mb-4" 
                    style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                    @if($announcement->category->icon)
                        <i class="{{ $announcement->category->icon }}"></i>
                    @endif
                    {{ $announcement->category->name }}
                </span>
            @endif
            
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-white mb-4">
                {{ $announcement->title }}
                @if($announcement->featured && setting('announcements_show_featured', true))
                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-sm font-medium bg-yellow-400 text-yellow-900 ml-2">
                        <i class="bi bi-star-fill"></i>
                    </span>
                @endif
            </h1>
            
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                @if(setting('announcements_show_date', true))
                    <span>
                        <i class="bi bi-calendar3 mr-1"></i>
                        {{ __('announcements::messages.front.published_on') }} {{ $announcement->published_at?->format('d/m/Y') }}
                    </span>
                @endif
                
                @if(setting('announcements_show_author', true))
                    <span>
                        <i class="bi bi-person mr-1"></i>
                        {{ __('announcements::messages.front.by') }} {{ $announcement->author_name }}
                    </span>
                @endif
                
                @if(setting('announcements_show_views', true))
                    <span>
                        <i class="bi bi-eye mr-1"></i>
                        {{ trans_choice('announcements::messages.front.views', $announcement->views_count, ['count' => number_format($announcement->views_count)]) }}
                    </span>
                @endif
            </div>
        </header>
        
        {{-- Cover image --}}
        @if($announcement->cover_image_display_url)
            <div class="mb-8 rounded-xl overflow-hidden max-h-96">
                <img src="{{ $announcement->cover_image_display_url }}" alt="{{ $announcement->title }}" class="w-full h-auto max-h-96 object-cover">
            </div>
        @endif
        
        {{-- Content --}}
        <div class="prose prose-lg max-w-none dark:prose-invert mb-8 
            prose-headings:text-gray-800 dark:prose-headings:text-gray-200
            prose-p:text-gray-600 dark:prose-p:text-gray-300
            prose-a:text-blue-600 dark:prose-a:text-blue-400
            prose-strong:text-gray-800 dark:prose-strong:text-gray-200
            prose-ul:text-gray-600 dark:prose-ul:text-gray-300
            prose-ol:text-gray-600 dark:prose-ol:text-gray-300
            prose-li:text-gray-600 dark:prose-li:text-gray-300
            prose-blockquote:text-gray-600 dark:prose-blockquote:text-gray-300
            prose-code:text-gray-800 dark:prose-code:text-gray-200
            prose-pre:bg-gray-100 dark:prose-pre:bg-gray-800">
            @if($announcement->rendered_content)
                {!! $announcement->rendered_content !!}
            @elseif($announcement->content_html)
                {!! $announcement->content_html !!}
            @elseif($announcement->content_markdown)
                {!! \Illuminate\Support\Str::markdown($announcement->content_markdown) !!}
            @else
                <p class="text-gray-500 italic">{{ __('announcements::messages.front.no_content') }}</p>
            @endif
        </div>
        
        {{-- Actions --}}
        <div class="flex items-center justify-between border-t border-b dark:border-gray-700 py-4 mb-8">
            <div class="flex items-center gap-4">
                @if(setting('announcements_likes_enabled', true))
                    <form id="like-form" method="POST" action="{{ route('announcements.like', $announcement->slug) }}" class="inline">
                        @csrf
                        <button type="submit" 
                            id="like-button"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg transition cursor-pointer {{ $announcement->hasLiked(auth()->id(), request()->ip()) ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' }}">
                            <i class="bi {{ $announcement->hasLiked(auth()->id(), request()->ip()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                            <span id="likes-count">{{ number_format($announcement->likes_count) }}</span>
                        </button>
                    </form>
                @endif
                
                <button type="button" id="share-button" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 cursor-pointer">
                    <i class="bi bi-share"></i>
                    {{ __('announcements::messages.front.share') }}
                </button>
            </div>
            
            <a href="{{ route('announcements.index') }}" class="text-blue-600 hover:text-blue-800">
                ← {{ __('announcements::messages.front.back_to_list') }}
            </a>
        </div>
        
        {{-- Navigation --}}
        @if(isset($previous) || isset($next))
            <div class="flex justify-between gap-4 mb-8">
                @if(isset($previous) && $previous)
                    <a href="{{ $previous->url }}" class="flex-1 p-4 rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        <span class="text-sm text-gray-500">← {{ __('announcements::messages.front.previous') }}</span>
                        <span class="block font-medium text-gray-800 dark:text-gray-200 truncate">{{ $previous->title }}</span>
                    </a>
                @else
                    <div class="flex-1"></div>
                @endif
                
                @if(isset($next) && $next)
                    <a href="{{ $next->url }}" class="flex-1 p-4 rounded-lg border dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition text-right">
                        <span class="text-sm text-gray-500">{{ __('announcements::messages.front.next') }} →</span>
                        <span class="block font-medium text-gray-800 dark:text-gray-200 truncate">{{ $next->title }}</span>
                    </a>
                @endif
            </div>
        @endif
        
        {{-- Related --}}
        @if(isset($related) && $related->count() > 0)
            <section class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">{{ __('announcements::messages.front.related') }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($related as $relatedAnnouncement)
                        <a href="{{ $relatedAnnouncement->url }}" class="group block p-4 rounded-lg border dark:border-gray-700 hover:shadow-md transition">
                            @if($relatedAnnouncement->cover_image_display_url)
                                <img src="{{ $relatedAnnouncement->cover_image_display_url }}" alt="" class="w-full h-32 object-cover rounded-lg mb-3">
                            @endif
                            <h3 class="font-medium text-gray-800 dark:text-gray-200 group-hover:text-blue-600 transition">{{ $relatedAnnouncement->title }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $relatedAnnouncement->published_at?->format('d/m/Y') }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </article>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Like form
        const likeForm = document.getElementById('like-form');
        if (likeForm) {
            likeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const button = document.getElementById('like-button');
                const formData = new FormData(form);
                
                // Disable button during request
                button.disabled = true;
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(data => {
                            throw data;
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        // Handle login required
                        if (data.requires_auth) {
                            if (confirm(data.error + '\n\n{{ __("announcements::messages.front.login_redirect") }}')) {
                                window.location.href = '{{ route("login") }}';
                            }
                        } else {
                            alert(data.error);
                        }
                        return;
                    }
                    
                    document.getElementById('likes-count').textContent = data.likes_count;
                    const icon = button.querySelector('i');
                    
                    if (data.liked) {
                        button.classList.remove('bg-gray-100', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:hover:bg-gray-700');
                        button.classList.add('bg-red-100', 'text-red-600', 'dark:bg-red-900/30', 'dark:text-red-400');
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill');
                    } else {
                        button.classList.add('bg-gray-100', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300', 'dark:hover:bg-gray-700');
                        button.classList.remove('bg-red-100', 'text-red-600', 'dark:bg-red-900/30', 'dark:text-red-400');
                        icon.classList.add('bi-heart');
                        icon.classList.remove('bi-heart-fill');
                    }
                })
                .catch(error => {
                    console.error('Like error:', error);
                    if (error.error) {
                        alert(error.error);
                    }
                })
                .finally(() => {
                    button.disabled = false;
                });
            });
        }
        
        // Share button
        const shareButton = document.getElementById('share-button');
        if (shareButton) {
            shareButton.addEventListener('click', function(e) {
                e.preventDefault();
                const shareData = {
                    title: '{{ addslashes($announcement->title) }}',
                    text: '{{ addslashes($announcement->excerpt ?? $announcement->title) }}',
                    url: '{{ $announcement->url }}'
                };
                
                if (navigator.share) {
                    navigator.share(shareData).catch(err => {
                        console.log('Share cancelled or failed:', err);
                    });
                } else {
                    // Fallback: copy to clipboard
                    navigator.clipboard.writeText(shareData.url).then(() => {
                        alert('{{ __("announcements::messages.front.link_copied") }}');
                    }).catch(() => {
                        // Final fallback: show URL
                        prompt('{{ __("announcements::messages.front.copy_link") }}', shareData.url);
                    });
                }
            });
        }
    });
</script>
@endsection
