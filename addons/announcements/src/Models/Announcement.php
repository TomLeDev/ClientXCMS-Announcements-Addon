<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Models;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $editor_mode
 * @property string|null $content_markdown
 * @property string|null $content_html
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property bool $featured
 * @property int $position
 * @property string|null $cover_image
 * @property int|null $category_id
 * @property int|null $author_id
 * @property bool $show_author
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property string|null $og_image
 * @property string|null $canonical_url
 * @property string $robots
 * @property int $views_count
 * @property int $likes_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read AnnouncementCategory|null $category
 * @property-read Admin|null $author
 */
class Announcement extends Model
{
    use SoftDeletes;

    protected $table = 'announcements';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'editor_mode',
        'content_markdown',
        'content_html',
        'status',
        'published_at',
        'featured',
        'position',
        'cover_image',
        'cover_image_url',
        'category_id',
        'author_id',
        'show_author',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'og_image_url',
        'canonical_url',
        'robots',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'show_author' => 'boolean',
        'position' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'published_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
        'editor_mode' => 'markdown',
        'featured' => false,
        'show_author' => true,
        'position' => 0,
        'views_count' => 0,
        'likes_count' => 0,
        'robots' => 'index,follow',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($announcement) {
            if (empty($announcement->slug)) {
                $announcement->slug = Str::slug($announcement->title);
            }
            // Ensure unique slug
            $originalSlug = $announcement->slug;
            $count = 1;
            while (static::withTrashed()->where('slug', $announcement->slug)->exists()) {
                $announcement->slug = $originalSlug . '-' . $count++;
            }
        });

        static::saving(function ($announcement) {
            // Auto-generate HTML from markdown if in markdown mode
            if ($announcement->editor_mode === 'markdown' && $announcement->content_markdown) {
                $announcement->content_html = static::convertMarkdownToHtml($announcement->content_markdown);
            }
        });
    }

    /**
     * Convert markdown to HTML.
     */
    public static function convertMarkdownToHtml(string $markdown): string
    {
        $environment = new Environment([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        
        $converter = new MarkdownConverter($environment);
        return $converter->convert($markdown)->getContent();
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(AnnouncementCategory::class, 'category_id');
    }

    /**
     * Get the author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    /**
     * Get the views.
     */
    public function views(): HasMany
    {
        return $this->hasMany(AnnouncementView::class, 'announcement_id');
    }

    /**
     * Get the likes.
     */
    public function likes(): HasMany
    {
        return $this->hasMany(AnnouncementLike::class, 'announcement_id');
    }

    /**
     * Get the cover image URL (prioritize manual URL over uploaded file).
     */
    public function getCoverImageDisplayUrlAttribute(): ?string
    {
        // Priority: manual URL > uploaded file
        if (!empty($this->cover_image_url)) {
            return $this->cover_image_url;
        }
        
        if (!empty($this->cover_image)) {
            return \Storage::url($this->cover_image);
        }
        
        return null;
    }

    /**
     * Get the OG image URL (uses cover image).
     */
    public function getOgImageDisplayUrlAttribute(): ?string
    {
        // Always use cover image for OG
        return $this->cover_image_display_url;
    }

    /**
     * Get rendered content.
     */
    public function getRenderedContentAttribute(): string
    {
        if ($this->editor_mode === 'html') {
            return $this->sanitizeHtml($this->content_html ?? '');
        }
        
        return $this->content_html ?? '';
    }

    /**
     * Sanitize HTML to remove potentially dangerous content.
     */
    protected function sanitizeHtml(string $html): string
    {
        // Remove script tags
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        // Remove event handlers
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        // Remove javascript: URLs
        $html = preg_replace('/javascript\s*:/i', '', $html);
        
        return $html;
    }

    /**
     * Get SEO title.
     */
    public function getSeoTitleAttribute(): string
    {
        if ($this->meta_title) {
            return $this->meta_title;
        }
        
        $template = setting('announcements_seo_title_template', '{title} - {site_name}');
        return str_replace(
            ['{title}', '{site_name}'],
            [$this->title, setting('app_name', 'ClientXCMS')],
            $template
        );
    }

    /**
     * Get SEO description.
     */
    public function getSeoDescriptionAttribute(): string
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }
        
        if ($this->excerpt) {
            return Str::limit(strip_tags($this->excerpt), 160);
        }
        
        return Str::limit(strip_tags($this->rendered_content), 160);
    }

    /**
     * Get OG image URL.
     */
    public function getOgImageUrlAttribute(): ?string
    {
        if ($this->og_image) {
            return \Storage::url($this->og_image);
        }
        
        if ($this->cover_image) {
            return $this->cover_image_url;
        }
        
        return setting('announcements_default_og_image');
    }

    /**
     * Get cover image URL.
     */
    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            // cover_image is stored as 'announcements/covers/file.jpg' (like groups/filename in ClientXCMS)
            return \Storage::url($this->cover_image);
        }
        
        return null;
    }

    /**
     * Get author name.
     */
    public function getAuthorNameAttribute(): string
    {
        if (!setting('announcements_show_author', true) || !$this->show_author) {
            return setting('announcements_anonymous_name', __('announcements::messages.staff'));
        }
        
        return $this->author?->username ?? setting('announcements_anonymous_name', __('announcements::messages.staff'));
    }

    /**
     * Scope for published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured announcements.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope for ordered announcements.
     */
    public function scopeOrdered($query, ?string $order = null)
    {
        $order = $order ?? setting('announcements_default_order', 'featured_position_date');
        
        switch ($order) {
            case 'featured_position_date':
                return $query->orderBy('featured', 'desc')
                    ->orderBy('position', 'asc')
                    ->orderBy('published_at', 'desc');
            case 'position_date':
                return $query->orderBy('position', 'asc')
                    ->orderBy('published_at', 'desc');
            case 'date':
            default:
                return $query->orderBy('published_at', 'desc');
        }
    }

    /**
     * Scope for searching.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
                ->orWhere('content_markdown', 'like', "%{$search}%")
                ->orWhere('content_html', 'like', "%{$search}%");
        });
    }

    /**
     * Check if announcement is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at <= now();
    }

    /**
     * Check if announcement is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' || 
            ($this->status === 'published' && $this->published_at && $this->published_at > now());
    }

    /**
     * Increment view count.
     */
    public function recordView(?int $userId = null, ?string $ip = null, ?string $userAgent = null, ?string $referer = null): void
    {
        $viewWindow = setting('announcements_view_window', 30); // minutes
        $ipHash = $ip ? hash('sha256', $ip) : null;
        $userAgentHash = $userAgent ? hash('sha256', $userAgent) : null;
        
        // Check for recent view
        $recentView = $this->views()
            ->where(function ($q) use ($userId, $ipHash) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('ip_hash', $ipHash);
                }
            })
            ->where('viewed_at', '>=', now()->subMinutes($viewWindow))
            ->exists();
        
        if (!$recentView) {
            $this->views()->create([
                'user_id' => $userId,
                'ip_hash' => $ipHash,
                'user_agent_hash' => $userAgentHash,
                'referer' => $referer ? Str::limit($referer, 255) : null,
                'viewed_at' => now(),
            ]);
            
            $this->increment('views_count');
        }
    }

    /**
     * Check if user has liked.
     */
    public function hasLiked(?int $userId = null, ?string $ip = null): bool
    {
        $ipHash = $ip ? hash('sha256', $ip) : null;
        
        return $this->likes()
            ->where(function ($q) use ($userId, $ipHash) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('ip_hash', $ipHash);
                }
            })
            ->exists();
    }

    /**
     * Toggle like.
     */
    public function toggleLike(?int $userId = null, ?string $ip = null, ?string $cookieId = null): bool
    {
        $ipHash = $ip ? hash('sha256', $ip) : null;
        
        $existingLike = $this->likes()
            ->where(function ($q) use ($userId, $ipHash) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('ip_hash', $ipHash);
                }
            })
            ->first();
        
        if ($existingLike) {
            $existingLike->delete();
            $this->decrement('likes_count');
            return false;
        }
        
        $this->likes()->create([
            'user_id' => $userId,
            'ip_hash' => $ipHash,
            'cookie_id' => $cookieId,
        ]);
        
        $this->increment('likes_count');
        return true;
    }

    /**
     * Get URL.
     */
    public function getUrlAttribute(): string
    {
        return route('announcements.show', $this->slug);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'published' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            'scheduled' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'archived' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return __('announcements::messages.statuses.' . $this->status);
    }
}
