<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Controllers\Admin;

use App\Addons\Announcements\Services\AnnouncementStatsService;
use App\Http\Controllers\Controller;
use App\Models\Admin\Permission;
use App\Models\Admin\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Show settings page.
     */
    public function index(Request $request = null)
    {
        if (!staff_has_permission(Permission::MANAGE_EXTENSIONS)) {
            abort(403);
        }
        
        $statsService = app(AnnouncementStatsService::class);
        $globalStats = $statsService->getGlobalStats('30d');
        
        return view('announcements_admin::settings.index', [
            'globalStats' => $globalStats,
            'translatePrefix' => 'announcements::messages.settings',
        ]);
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        if (!staff_has_permission(Permission::MANAGE_EXTENSIONS)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'announcements_enabled' => ['nullable'],
            'announcements_likes_enabled' => ['nullable'],
            'announcements_likes_mode' => ['nullable', 'in:all,authenticated,ip'],
            'announcements_show_views' => ['nullable'],
            'announcements_show_author' => ['nullable'],
            'announcements_show_date' => ['nullable'],
            'announcements_show_featured' => ['nullable'],
            'announcements_scheduling_enabled' => ['nullable'],
            'announcements_per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'announcements_default_order' => ['nullable', 'in:featured_position_date,position_date,date'],
            'announcements_seo_title_template' => ['nullable', 'string', 'max:255'],
            'announcements_default_meta_description' => ['nullable', 'string', 'max:255'],
            'announcements_default_og_image' => ['nullable', 'string', 'max:255'],
            'announcements_view_mode' => ['nullable', 'in:total,unique,authenticated'],
            'announcements_view_window' => ['nullable', 'integer', 'min:1', 'max:1440'],
            'announcements_anonymous_name' => ['nullable', 'string', 'max:50'],
            'announcements_public_url' => ['nullable', 'string', 'max:50', 'regex:/^[a-z0-9-]+$/'],
        ]);
        
        // Convert checkboxes to boolean strings
        $checkboxFields = [
            'announcements_enabled',
            'announcements_likes_enabled',
            'announcements_show_views',
            'announcements_show_author',
            'announcements_show_date',
            'announcements_show_featured',
            'announcements_scheduling_enabled',
        ];
        
        $settings = [];
        foreach ($validated as $key => $value) {
            if (in_array($key, $checkboxFields)) {
                $settings[$key] = $request->has($key) ? '1' : '0';
            } else {
                $settings[$key] = $value ?? '';
            }
        }
        
        // Use ClientXCMS native method
        Setting::updateSettings($settings);
        
        return back()->with('success', __('announcements::messages.settings.updated'));
    }
}
