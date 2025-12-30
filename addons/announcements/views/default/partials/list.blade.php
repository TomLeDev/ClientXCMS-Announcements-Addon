@if($announcements->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($announcements as $announcement)
            <article class="group flex flex-col bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden dark:bg-slate-900 dark:border-gray-700 hover:shadow-lg transition">
                {{-- Image ou placeholder --}}
                <div class="relative pt-[50%] overflow-hidden bg-gray-100 dark:bg-gray-800">
                    @if($announcement->cover_image_display_url)
                        <img class="absolute top-0 start-0 object-cover w-full h-full group-hover:scale-105 transition-transform duration-500" 
                            src="{{ $announcement->cover_image_display_url }}" alt="{{ $announcement->title }}">
                    @else
                        <div class="absolute top-0 start-0 w-full h-full flex items-center justify-center">
                            <i class="bi bi-megaphone text-4xl text-gray-300 dark:text-gray-600"></i>
                        </div>
                    @endif
                    @if($announcement->featured && setting('announcements_show_featured', true))
                        <span class="absolute top-3 start-3 inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-yellow-400 text-yellow-900">
                            <i class="bi bi-star-fill"></i>
                            {{ __('announcements::messages.front.featured') }}
                        </span>
                    @endif
                </div>
                
                <div class="p-4 flex flex-col flex-1">
                    @if($announcement->category)
                        <span class="inline-flex items-center gap-1 self-start px-2 py-1 rounded-full text-xs font-medium mb-2" 
                            style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                            @if($announcement->category->icon)
                                <i class="{{ $announcement->category->icon }}"></i>
                            @endif
                            {{ $announcement->category->name }}
                        </span>
                    @endif
                    
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 group-hover:text-blue-600 transition">
                        <a href="{{ $announcement->url }}">{{ $announcement->title }}</a>
                    </h3>
                    
                    @if($announcement->excerpt)
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-3">
                            {{ $announcement->excerpt }}
                        </p>
                    @endif
                    
                    <div class="mt-auto pt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-3">
                            @if(setting('announcements_show_date', true))
                                <span title="{{ __('announcements::messages.front.published_on') }}">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $announcement->published_at?->format('d/m/Y') }}
                                </span>
                            @endif
                            @if(setting('announcements_show_views', true))
                                <span>
                                    <i class="bi bi-eye"></i>
                                    {{ number_format($announcement->views_count) }}
                                </span>
                            @endif
                            @if(setting('announcements_likes_enabled', true))
                                <span>
                                    <i class="bi bi-heart"></i>
                                    {{ number_format($announcement->likes_count) }}
                                </span>
                            @endif
                        </div>
                        
                        <a href="{{ $announcement->url }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ __('announcements::messages.front.read_more') }} →
                        </a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@else
    <div class="text-center py-12">
        <i class="bi bi-megaphone text-6xl text-gray-300 dark:text-gray-600 mb-4 block"></i>
        <p class="text-gray-500 dark:text-gray-400">{{ __('announcements::messages.front.no_results') }}</p>
    </div>
@endif
