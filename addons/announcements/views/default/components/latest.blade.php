@php
    use App\Addons\Announcements\Models\Announcement;
    use App\Addons\Announcements\Models\AnnouncementCategory;
    
    $limit = $limit ?? 5;
    $category = $category ?? null;
    $featuredOnly = $featuredOnly ?? false;
    $showViewAll = $showViewAll ?? true;
    
    $query = Announcement::published()->with('category')->ordered();
    
    if ($featuredOnly) {
        $query->featured();
    }
    
    if ($category) {
        $categoryModel = AnnouncementCategory::where('slug', $category)->first();
        if ($categoryModel) {
            $query->where('category_id', $categoryModel->id);
        }
    }
    
    $announcements = $query->limit($limit)->get();
@endphp

@if($announcements->count() > 0)
    <div class="announcements-latest">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                {{ __('announcements::messages.components.latest.title') }}
            </h3>
            @if($showViewAll)
                <a href="{{ route('announcements.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    {{ __('announcements::messages.components.latest.view_all') }} →
                </a>
            @endif
        </div>
        
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <a href="{{ $announcement->url }}" class="group flex gap-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    @if($announcement->cover_image_display_url)
                        <img src="{{ $announcement->cover_image_display_url }}" alt="" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-megaphone text-gray-400"></i>
                        </div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            @if($announcement->category)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium" 
                                    style="background-color: {{ $announcement->category->color }}20; color: {{ $announcement->category->color }}">
                                    {{ $announcement->category->name }}
                                </span>
                            @endif
                            @if($announcement->featured)
                                <i class="bi bi-star-fill text-yellow-500 text-xs"></i>
                            @endif
                        </div>
                        
                        <h4 class="font-medium text-gray-800 dark:text-gray-200 group-hover:text-blue-600 truncate transition">
                            {{ $announcement->title }}
                        </h4>
                        
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $announcement->published_at?->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@else
    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
        <i class="bi bi-megaphone text-4xl mb-2"></i>
        <p>{{ __('announcements::messages.components.latest.no_announcements') }}</p>
    </div>
@endif
