<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property int $position
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Announcement[] $announcements
 */
class AnnouncementCategory extends Model
{
    protected $table = 'announcement_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    protected $attributes = [
        'color' => '#3b82f6',
        'position' => 0,
        'is_active' => true,
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
            // Ensure unique slug
            $originalSlug = $category->slug;
            $count = 1;
            while (static::where('slug', $category->slug)->exists()) {
                $category->slug = $originalSlug . '-' . $count++;
            }
        });
    }

    /**
     * Get announcements for this category.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'category_id');
    }

    /**
     * Get published announcements count.
     */
    public function getPublishedCountAttribute(): int
    {
        return $this->announcements()
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->count();
    }

    /**
     * Scope for active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered categories.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc')->orderBy('name', 'asc');
    }
}
