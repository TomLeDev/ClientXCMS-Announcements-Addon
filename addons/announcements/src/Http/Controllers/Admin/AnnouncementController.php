<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Controllers\Admin;

use App\Addons\Announcements\Http\Requests\AnnouncementRequest;
use App\Addons\Announcements\Models\Announcement;
use App\Addons\Announcements\Models\AnnouncementCategory;
use App\Addons\Announcements\Services\AnnouncementStatsService;
use App\Http\Controllers\Admin\AbstractCrudController;
use App\Models\Admin\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AnnouncementController extends AbstractCrudController
{
    protected string $model = Announcement::class;
    protected string $viewPath = 'announcements_admin::announcements';
    protected string $translatePrefix = 'announcements::messages.admin';
    protected string $routePath = 'admin.announcements';
    protected bool $extensionPermission = true;
    protected int $perPage = 20;
    protected string $searchField = 'title';
    protected string $filterField = 'status';
    protected array $relations = ['category', 'author'];

    /**
     * Get index filters.
     */
    protected function getIndexFilters()
    {
        return [
            'draft' => __('announcements::messages.statuses.draft'),
            'published' => __('announcements::messages.statuses.published'),
            'scheduled' => __('announcements::messages.statuses.scheduled'),
            'archived' => __('announcements::messages.statuses.archived'),
        ];
    }

    /**
     * Get index params.
     */
    protected function getIndexParams($items, string $translatePrefix)
    {
        $params = parent::getIndexParams($items, $translatePrefix);
        $params['categories'] = AnnouncementCategory::ordered()->get();
        return $params;
    }

    /**
     * Get create params.
     */
    public function getCreateParams()
    {
        $params = parent::getCreateParams();
        $params['categories'] = AnnouncementCategory::active()->ordered()->pluck('name', 'id');
        $params['statuses'] = $this->getStatusOptions();
        return $params;
    }

    /**
     * Get show/edit view params.
     */
    public function showView(array $params)
    {
        $params['categories'] = AnnouncementCategory::active()->ordered()->pluck('name', 'id');
        $params['statuses'] = $this->getStatusOptions();
        return parent::showView($params);
    }

    /**
     * Get status options.
     */
    protected function getStatusOptions(): array
    {
        return [
            'draft' => __('announcements::messages.statuses.draft'),
            'published' => __('announcements::messages.statuses.published'),
            'scheduled' => __('announcements::messages.statuses.scheduled'),
            'archived' => __('announcements::messages.statuses.archived'),
        ];
    }

    /**
     * Store a new announcement.
     */
    public function store(AnnouncementRequest $request)
    {
        $announcement = $request->store();
        
        return $this->storeRedirect($announcement);
    }

    /**
     * Show an announcement.
     */
    public function show(Announcement $announcement)
    {
        return $this->showView(['item' => $announcement]);
    }

    /**
     * Update an announcement.
     */
    public function update(AnnouncementRequest $request, Announcement $announcement)
    {
        $request->update();
        
        return $this->updateRedirect($announcement);
    }

    /**
     * Delete an announcement.
     */
    public function destroy(Announcement $announcement)
    {
        // Delete associated images
        if ($announcement->cover_image) {
            \Storage::delete('public/' . $announcement->cover_image);
        }
        if ($announcement->og_image) {
            \Storage::delete('public/' . $announcement->og_image);
        }
        
        $announcement->delete();
        
        return $this->deleteRedirect($announcement);
    }

    /**
     * Duplicate an announcement.
     */
    public function duplicate(Announcement $announcement)
    {
        $new = $announcement->replicate();
        $new->title = $announcement->title . ' (Copy)';
        $new->slug = Str::slug($new->title);
        $new->status = 'draft';
        $new->views_count = 0;
        $new->likes_count = 0;
        $new->published_at = null;
        $new->author_id = auth('admin')->id();
        $new->save();
        
        return redirect()->route('admin.announcements.show', $new)
            ->with('success', __('announcements::messages.admin.duplicated'));
    }

    /**
     * Update positions (AJAX).
     */
    public function updatePositions(Request $request)
    {
        $positions = $request->input('positions', []);
        
        foreach ($positions as $id => $position) {
            Announcement::where('id', $id)->update(['position' => $position]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Show stats for an announcement.
     */
    public function stats(Announcement $announcement, AnnouncementStatsService $statsService)
    {
        $period = request('period', '30d');
        $stats = $statsService->getStats($announcement, $period);
        
        return view('announcements_admin::announcements.stats', [
            'item' => $announcement,
            'stats' => $stats,
            'period' => $period,
            'translatePrefix' => $this->translatePrefix,
            'routePath' => $this->routePath,
        ]);
    }

    /**
     * Export stats to CSV.
     */
    public function exportStats(Announcement $announcement, AnnouncementStatsService $statsService)
    {
        $period = request('period', '30d');
        $csv = $statsService->exportToCsv($announcement, $period);
        
        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="stats-' . $announcement->slug . '-' . $period . '.csv"');
    }

    /**
     * Preview an announcement.
     */
    public function preview(Announcement $announcement)
    {
        return view('announcements::show', [
            'announcement' => $announcement,
            'preview' => true,
        ]);
    }

    /**
     * Quick publish/unpublish toggle.
     */
    public function togglePublish(Announcement $announcement)
    {
        if ($announcement->status === 'published') {
            $announcement->update(['status' => 'draft']);
            $message = __('announcements::messages.admin.unpublished');
        } else {
            $announcement->update([
                'status' => 'published',
                'published_at' => $announcement->published_at ?? now(),
            ]);
            $message = __('announcements::messages.admin.published');
        }
        
        return back()->with('success', $message);
    }

    /**
     * Reorder page.
     */
    public function reorder()
    {
        $announcements = Announcement::orderBy('featured', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('published_at', 'desc')
            ->get();
        
        return view('announcements_admin::announcements.reorder', [
            'items' => $announcements,
            'translatePrefix' => $this->translatePrefix,
            'routePath' => $this->routePath,
        ]);
    }

    /**
     * Remove cover image.
     */
    public function removeCoverImage(Announcement $announcement)
    {
        if ($announcement->cover_image) {
            if (\Storage::exists('public/' . $announcement->cover_image)) {
                \Storage::delete('public/' . $announcement->cover_image);
            }
            $announcement->update(['cover_image' => null]);
        }
        
        return back()->with('success', __('announcements::messages.admin.cover_removed'));
    }
}
