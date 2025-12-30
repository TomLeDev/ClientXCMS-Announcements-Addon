<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Controllers\Front;

use App\Addons\Announcements\Models\Announcement;
use App\Addons\Announcements\Models\AnnouncementCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Show announcements listing.
     */
    public function index(Request $request)
    {
        if (!setting('announcements_enabled', true)) {
            abort(404);
        }
        
        $query = Announcement::published()
            ->with('category', 'author')
            ->ordered();
        
        // Filter by category
        if ($request->has('category') && $request->input('category')) {
            $category = AnnouncementCategory::where('slug', $request->input('category'))->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        // Search
        if ($request->has('q') && $search = $request->input('q')) {
            $query->search($search);
        }
        
        $perPage = setting('announcements_per_page', 12);
        $announcements = $query->paginate($perPage)->appends($request->query());
        $categories = AnnouncementCategory::active()->ordered()->withCount([
            'announcements' => function ($q) {
                $q->published();
            }
        ])->get();
        
        // If AJAX request, return partial
        if ($request->ajax()) {
            return view('announcements::partials.list', [
                'announcements' => $announcements,
            ]);
        }
        
        return view('announcements::index', [
            'announcements' => $announcements,
            'categories' => $categories,
            'currentCategory' => $request->input('category'),
            'searchQuery' => $request->input('q'),
        ]);
    }

    /**
     * Search announcements (AJAX).
     */
    public function search(Request $request)
    {
        if (!setting('announcements_enabled', true)) {
            return response()->json(['announcements' => []]);
        }
        
        $query = Announcement::published()
            ->with('category')
            ->ordered();
        
        if ($search = $request->input('q')) {
            $query->search($search);
        }
        
        if ($categorySlug = $request->input('category')) {
            $category = AnnouncementCategory::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }
        
        $announcements = $query->limit(10)->get();
        
        return response()->json([
            'announcements' => $announcements->map(function ($a) {
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'excerpt' => $a->excerpt,
                    'url' => $a->url,
                    'cover_image_url' => $a->cover_image_url,
                    'category' => $a->category?->name,
                    'category_color' => $a->category?->color,
                    'published_at' => $a->published_at?->format('d/m/Y'),
                    'views_count' => $a->views_count,
                ];
            }),
        ]);
    }

    /**
     * Show single announcement.
     */
    public function show(string $slug)
    {
        if (!setting('announcements_enabled', true)) {
            abort(404);
        }
        
        $announcement = Announcement::where('slug', $slug)->first();
        
        if (!$announcement) {
            abort(404);
        }
        
        // Check if published or if admin viewing
        if (!$announcement->isPublished() && !auth('admin')->check()) {
            abort(404);
        }
        
        // Record view
        $announcement->recordView(
            auth()->id(),
            request()->ip(),
            request()->userAgent(),
            request()->header('referer')
        );
        
        // Get related announcements
        $related = Announcement::published()
            ->where('id', '!=', $announcement->id)
            ->when($announcement->category_id, function ($q) use ($announcement) {
                $q->where('category_id', $announcement->category_id);
            })
            ->ordered()
            ->limit(3)
            ->get();
        
        // Get previous and next
        $previous = Announcement::published()
            ->where('published_at', '<', $announcement->published_at)
            ->orderBy('published_at', 'desc')
            ->first();
        
        $next = Announcement::published()
            ->where('published_at', '>', $announcement->published_at)
            ->orderBy('published_at', 'asc')
            ->first();
        
        return view('announcements::show', [
            'announcement' => $announcement,
            'related' => $related,
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    /**
     * Toggle like (AJAX).
     */
    public function toggleLike(string $slug, Request $request)
    {
        // Check if likes are enabled
        if (!setting('announcements_likes_enabled', true)) {
            return response()->json([
                'error' => __('announcements::messages.front.likes_disabled'),
                'message' => 'Likes are disabled'
            ], 403);
        }
        
        $announcement = Announcement::where('slug', $slug)->firstOrFail();
        
        // Get likes mode
        $likesMode = setting('announcements_likes_mode', 'all');
        
        // Check authentication based on mode
        if ($likesMode === 'authenticated' && !auth()->check()) {
            return response()->json([
                'error' => __('announcements::messages.front.login_required'),
                'message' => 'Authentication required',
                'requires_auth' => true
            ], 401);
        }
        
        // Determine identification method
        $userId = null;
        $ip = $request->ip();
        
        if ($likesMode === 'authenticated') {
            // Only use user ID for authenticated mode
            $userId = auth()->id();
            $ip = null;
        } elseif ($likesMode === 'ip') {
            // Only use IP
            $userId = null;
        } else {
            // 'all' mode - use user ID if logged in, otherwise IP
            $userId = auth()->id();
        }
        
        $liked = $announcement->toggleLike(
            $userId,
            $ip,
            $request->cookie('announcements_visitor_id')
        );
        
        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $announcement->fresh()->likes_count,
        ]);
    }

    /**
     * RSS feed.
     */
    public function rss()
    {
        if (!setting('announcements_enabled', true)) {
            abort(404);
        }
        
        $announcements = Announcement::published()
            ->ordered()
            ->limit(20)
            ->get();
        
        $content = view('announcements::rss', [
            'announcements' => $announcements,
            'siteName' => setting('app_name', 'ClientXCMS'),
            'siteUrl' => config('app.url'),
        ])->render();
        
        return response($content)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
