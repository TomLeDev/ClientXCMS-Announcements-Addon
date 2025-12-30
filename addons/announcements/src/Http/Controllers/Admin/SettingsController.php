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
use Illuminate\Support\Facades\Http;

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
            // Discord settings
            'announcements_discord_enabled' => ['nullable'],
            'announcements_discord_webhook_url' => ['nullable', 'url', 'max:500'],
            'announcements_discord_username' => ['nullable', 'string', 'max:80'],
            'announcements_discord_avatar_url' => ['nullable', 'url', 'max:500'],
            'announcements_discord_content' => ['nullable', 'string', 'max:2000'],
            'announcements_discord_embed_title' => ['nullable', 'string', 'max:256'],
            'announcements_discord_embed_description' => ['nullable', 'string', 'max:4096'],
            'announcements_discord_embed_color' => ['nullable', 'string', 'max:10'],
            'announcements_discord_embed_footer' => ['nullable', 'string', 'max:2048'],
            'announcements_discord_embed_footer_icon' => ['nullable', 'url', 'max:500'],
            'announcements_discord_embed_thumbnail' => ['nullable', 'url', 'max:500'],
            'announcements_discord_embed_timestamp' => ['nullable'],
            'announcements_discord_embed_image' => ['nullable'],
            'announcements_discord_embed_author' => ['nullable'],
            'announcements_discord_embed_field_category' => ['nullable'],
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
            'announcements_discord_enabled',
            'announcements_discord_embed_timestamp',
            'announcements_discord_embed_image',
            'announcements_discord_embed_author',
            'announcements_discord_embed_field_category',
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

    /**
     * Test Discord webhook.
     */
    public function testDiscord(Request $request)
    {
        if (!staff_has_permission(Permission::MANAGE_EXTENSIONS)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
        }
        
        $request->validate([
            'webhook_url' => ['required', 'url'],
        ]);
        
        try {
            $payload = [
                'content' => '🔔 Test de webhook depuis ' . setting('app_name', 'ClientXCMS'),
                'embeds' => [
                    [
                        'title' => 'Test de notification',
                        'description' => 'Ceci est un test du webhook Discord pour les annonces. Si vous voyez ce message, le webhook fonctionne correctement !',
                        'color' => hexdec('3b82f6'),
                        'timestamp' => now()->toIso8601String(),
                        'footer' => [
                            'text' => 'Module Annonces - ClientXCMS',
                        ],
                    ],
                ],
            ];
            
            $response = Http::post($request->input('webhook_url'), $payload);
            
            if ($response->successful()) {
                return response()->json(['success' => true]);
            }
            
            return response()->json([
                'success' => false, 
                'error' => 'HTTP ' . $response->status()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'error' => $e->getMessage()
            ]);
        }
    }
}
