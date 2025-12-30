<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Services;

use App\Addons\Announcements\Models\Announcement;
use App\Addons\Announcements\Models\AnnouncementView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AnnouncementStatsService
{
    /**
     * Get stats for an announcement.
     */
    public function getStats(Announcement $announcement, string $period = '30d'): array
    {
        $dates = $this->getPeriodDates($period);
        
        return [
            'total_views' => $announcement->views_count,
            'unique_views' => $this->getUniqueViews($announcement, $dates['start']),
            'total_likes' => $announcement->likes_count,
            'views_by_day' => $this->getCumulativeViewsByDay($announcement, $dates['start'], $dates['end']),
            'likes_by_day' => $this->getCumulativeLikesByDay($announcement, $dates['start'], $dates['end']),
            'top_referers' => $this->getTopReferers($announcement, $dates['start']),
            'period' => $period,
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
        ];
    }

    /**
     * Get period dates.
     */
    protected function getPeriodDates(string $period): array
    {
        $end = now()->endOfDay();
        
        $start = match($period) {
            '7d' => now()->subDays(7)->startOfDay(),
            '30d' => now()->subDays(30)->startOfDay(),
            '90d' => now()->subDays(90)->startOfDay(),
            '1y' => now()->subYear()->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };
        
        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * Get unique views count.
     */
    protected function getUniqueViews(Announcement $announcement, $startDate): int
    {
        return $announcement->views()
            ->where('viewed_at', '>=', $startDate)
            ->distinct('ip_hash')
            ->count('ip_hash');
    }

    /**
     * Get cumulative views by day (total running).
     */
    protected function getCumulativeViewsByDay(Announcement $announcement, $startDate, $endDate): Collection
    {
        // Get daily views
        $dailyViews = $announcement->views()
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('viewed_at', '>=', $startDate)
            ->where('viewed_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        // Get views before start date (baseline)
        $baselineViews = $announcement->views()
            ->where('viewed_at', '<', $startDate)
            ->count();
        
        // Build cumulative result
        $result = collect();
        $current = $startDate->copy();
        $cumulative = $baselineViews;
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $dayViews = $dailyViews->get($dateStr)?->count ?? 0;
            $cumulative += $dayViews;
            
            $result->push([
                'date' => $dateStr,
                'count' => $cumulative,
            ]);
            $current->addDay();
        }
        
        return $result;
    }

    /**
     * Get cumulative likes by day (total running).
     */
    protected function getCumulativeLikesByDay(Announcement $announcement, $startDate, $endDate): Collection
    {
        // Get daily likes
        $dailyLikes = $announcement->likes()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        // Get likes before start date (baseline)
        $baselineLikes = $announcement->likes()
            ->where('created_at', '<', $startDate)
            ->count();
        
        // Build cumulative result
        $result = collect();
        $current = $startDate->copy();
        $cumulative = $baselineLikes;
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $dayLikes = $dailyLikes->get($dateStr)?->count ?? 0;
            $cumulative += $dayLikes;
            
            $result->push([
                'date' => $dateStr,
                'count' => $cumulative,
            ]);
            $current->addDay();
        }
        
        return $result;
    }

    /**
     * Get top referers.
     */
    protected function getTopReferers(Announcement $announcement, $startDate, int $limit = 10): Collection
    {
        return $announcement->views()
            ->select('referer', DB::raw('COUNT(*) as count'))
            ->where('viewed_at', '>=', $startDate)
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get global stats.
     */
    public function getGlobalStats(string $period = '30d'): array
    {
        $dates = $this->getPeriodDates($period);
        
        return [
            'total_announcements' => Announcement::count(),
            'published_announcements' => Announcement::published()->count(),
            'total_views' => AnnouncementView::where('viewed_at', '>=', $dates['start'])->count(),
            'total_likes' => Announcement::sum('likes_count'),
            'most_viewed' => Announcement::published()
                ->orderByDesc('views_count')
                ->limit(5)
                ->get(['id', 'title', 'slug', 'views_count']),
            'most_liked' => Announcement::published()
                ->orderByDesc('likes_count')
                ->limit(5)
                ->get(['id', 'title', 'slug', 'likes_count']),
        ];
    }

    /**
     * Export stats to CSV.
     */
    public function exportToCsv(Announcement $announcement, string $period = '30d'): string
    {
        $stats = $this->getStats($announcement, $period);
        
        $csv = "Date,Total Views,Total Likes\n";
        
        $likesMap = collect($stats['likes_by_day'])->keyBy('date');
        
        foreach ($stats['views_by_day'] as $day) {
            $likes = $likesMap->get($day['date'])['count'] ?? 0;
            $csv .= "{$day['date']},{$day['count']},{$likes}\n";
        }
        
        return $csv;
    }

    /**
     * Send Discord webhook notification for new announcement.
     */
    public function sendDiscordNotification(Announcement $announcement): bool
    {
        $webhookUrl = setting('announcements_discord_webhook_url');
        
        if (empty($webhookUrl)) {
            return false;
        }
        
        if (!setting('announcements_discord_enabled', false)) {
            return false;
        }
        
        try {
            $embed = $this->buildDiscordEmbed($announcement);
            
            $payload = [
                'embeds' => [$embed],
            ];
            
            // Add custom content if configured
            $content = $this->replaceVariables(
                setting('announcements_discord_content', ''),
                $announcement
            );
            
            if (!empty($content)) {
                $payload['content'] = $content;
            }
            
            // Add username if configured
            $username = setting('announcements_discord_username');
            if (!empty($username)) {
                $payload['username'] = $username;
            }
            
            // Add avatar if configured
            $avatarUrl = setting('announcements_discord_avatar_url');
            if (!empty($avatarUrl)) {
                $payload['avatar_url'] = $avatarUrl;
            }
            
            $response = Http::post($webhookUrl, $payload);
            
            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('Discord webhook failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Build Discord embed from announcement.
     */
    protected function buildDiscordEmbed(Announcement $announcement): array
    {
        $embed = [];
        
        // Title
        $titleTemplate = setting('announcements_discord_embed_title', '{title}');
        $embed['title'] = $this->replaceVariables($titleTemplate, $announcement);
        
        // Description
        $descTemplate = setting('announcements_discord_embed_description', '{excerpt}');
        $embed['description'] = $this->replaceVariables($descTemplate, $announcement);
        
        // URL
        $embed['url'] = $announcement->url;
        
        // Color
        $color = setting('announcements_discord_embed_color', '#3b82f6');
        $embed['color'] = hexdec(ltrim($color, '#'));
        
        // Timestamp
        if (setting('announcements_discord_embed_timestamp', true)) {
            $embed['timestamp'] = $announcement->published_at?->toIso8601String() ?? now()->toIso8601String();
        }
        
        // Author
        if (setting('announcements_discord_embed_author', false)) {
            $embed['author'] = [
                'name' => $announcement->author_name,
            ];
            
            $authorIconUrl = setting('announcements_discord_embed_author_icon');
            if (!empty($authorIconUrl)) {
                $embed['author']['icon_url'] = $authorIconUrl;
            }
        }
        
        // Footer
        $footerText = setting('announcements_discord_embed_footer', '');
        if (!empty($footerText)) {
            $embed['footer'] = [
                'text' => $this->replaceVariables($footerText, $announcement),
            ];
            
            $footerIconUrl = setting('announcements_discord_embed_footer_icon');
            if (!empty($footerIconUrl)) {
                $embed['footer']['icon_url'] = $footerIconUrl;
            }
        }
        
        // Thumbnail
        $thumbnailUrl = setting('announcements_discord_embed_thumbnail');
        if (!empty($thumbnailUrl)) {
            $embed['thumbnail'] = ['url' => $thumbnailUrl];
        }
        
        // Image (cover image)
        if (setting('announcements_discord_embed_image', true) && $announcement->cover_image_display_url) {
            // Make sure URL is absolute
            $imageUrl = $announcement->cover_image_display_url;
            if (!str_starts_with($imageUrl, 'http')) {
                $imageUrl = url($imageUrl);
            }
            $embed['image'] = ['url' => $imageUrl];
        }
        
        // Fields
        $fields = [];
        
        if (setting('announcements_discord_embed_field_category', true) && $announcement->category) {
            $fields[] = [
                'name' => __('announcements::messages.admin.fields.category'),
                'value' => $announcement->category->name,
                'inline' => true,
            ];
        }
        
        if (setting('announcements_discord_embed_field_author', false) && $announcement->show_author) {
            $fields[] = [
                'name' => __('announcements::messages.admin.fields.author'),
                'value' => $announcement->author_name,
                'inline' => true,
            ];
        }
        
        // Custom fields
        $customFields = setting('announcements_discord_embed_custom_fields', '');
        if (!empty($customFields)) {
            $customFieldsArray = json_decode($customFields, true);
            if (is_array($customFieldsArray)) {
                foreach ($customFieldsArray as $field) {
                    if (!empty($field['name']) && !empty($field['value'])) {
                        $fields[] = [
                            'name' => $this->replaceVariables($field['name'], $announcement),
                            'value' => $this->replaceVariables($field['value'], $announcement),
                            'inline' => $field['inline'] ?? false,
                        ];
                    }
                }
            }
        }
        
        if (!empty($fields)) {
            $embed['fields'] = $fields;
        }
        
        return $embed;
    }

    /**
     * Replace variables in template string.
     */
    protected function replaceVariables(string $template, Announcement $announcement): string
    {
        $variables = [
            '{title}' => $announcement->title,
            '{slug}' => $announcement->slug,
            '{excerpt}' => $announcement->excerpt ?? Str::limit(strip_tags($announcement->rendered_content), 200),
            '{url}' => $announcement->url,
            '{author}' => $announcement->author_name,
            '{category}' => $announcement->category?->name ?? '',
            '{status}' => $announcement->status_label,
            '{published_at}' => $announcement->published_at?->format('d/m/Y H:i') ?? '',
            '{views}' => number_format($announcement->views_count),
            '{likes}' => number_format($announcement->likes_count),
            '{site_name}' => setting('app_name', 'ClientXCMS'),
            '{site_url}' => url('/'),
        ];
        
        return str_replace(array_keys($variables), array_values($variables), $template);
    }
}
