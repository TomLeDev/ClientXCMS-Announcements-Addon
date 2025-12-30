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
            'views_by_day' => $this->getViewsByDay($announcement, $dates['start'], $dates['end']),
            'likes_by_day' => $this->getLikesByDay($announcement, $dates['start'], $dates['end']),
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
     * Get views by day.
     */
    protected function getViewsByDay(Announcement $announcement, $startDate, $endDate): Collection
    {
        $views = $announcement->views()
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('viewed_at', '>=', $startDate)
            ->where('viewed_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        // Fill missing dates with 0
        $result = collect();
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $result->push([
                'date' => $dateStr,
                'count' => $views->get($dateStr)?->count ?? 0,
            ]);
            $current->addDay();
        }
        
        return $result;
    }

    /**
     * Get likes by day.
     */
    protected function getLikesByDay(Announcement $announcement, $startDate, $endDate): Collection
    {
        $likes = $announcement->likes()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
        
        // Fill missing dates with 0
        $result = collect();
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            $result->push([
                'date' => $dateStr,
                'count' => $likes->get($dateStr)?->count ?? 0,
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
        
        $csv = "Date,Views,Likes\n";
        
        $likesMap = collect($stats['likes_by_day'])->keyBy('date');
        
        foreach ($stats['views_by_day'] as $day) {
            $likes = $likesMap->get($day['date'])?->count ?? 0;
            $csv .= "{$day['date']},{$day['count']},{$likes}\n";
        }
        
        return $csv;
    }
}
