<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements;

use App\Addons\Announcements\Console\Commands\PublishScheduledAnnouncementsCommand;
use App\Addons\Announcements\Database\Seeders\AnnouncementsDatabaseSeeder;
use App\Addons\Announcements\Http\Controllers\Admin\AnnouncementController;
use App\Addons\Announcements\Http\Controllers\Admin\CategoryController;
use App\Addons\Announcements\Http\Controllers\Admin\SettingsController;
use App\Addons\Announcements\Models\Announcement;
use App\Addons\Announcements\Services\AnnouncementStatsService;
use App\Core\Admin\Dashboard\AdminCountWidget;
use App\Extensions\BaseAddonServiceProvider;
use App\Models\Admin\Permission;
use App\Services\SettingsService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class AnnouncementsServiceProvider extends BaseAddonServiceProvider
{
    protected string $uuid = 'announcements';

    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(AnnouncementStatsService::class, function () {
            return new AnnouncementStatsService();
        });
        
        // Register commands
        $this->commands([
            PublishScheduledAnnouncementsCommand::class,
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        $this->loadViews();
        $this->loadMigrations();
        $this->loadTranslations();
        
        // Add seeders using the proper method
        $this->addSeeders([
            \App\Addons\Announcements\Database\Seeders\AnnouncementCategorySeeder::class,
            \App\Addons\Announcements\Database\Seeders\AnnouncementSeeder::class,
        ]);
        
        // Initialize default settings
        $this->initializeSettings();
        
        // Register admin routes
        Route::middleware(['web', 'admin'])
            ->prefix(admin_prefix())
            ->name('admin.')
            ->group(function () {
                require addon_path('announcements', 'routes/admin.php');
            });
        
        // Register public routes
        Route::middleware(['web'])
            ->group(function () {
                require addon_path('announcements', 'routes/web.php');
            });
        
        // Register API routes
        Route::middleware(['api', 'auth:sanctum'])
            ->prefix('api/application')
            ->name('api.application.')
            ->group(function () {
                require addon_path('announcements', 'routes/api.php');
            });
        
        // Add settings cards/items
        $this->registerSettingsItems();
        
        // Add dashboard widget
        $this->registerDashboardWidget();
        
        // Register Blade components
        $this->registerBladeDirectives();
        
        // Schedule the publish command
        $this->scheduleCommands();
    }
    
    /**
     * Schedule commands for publishing scheduled announcements.
     */
    protected function scheduleCommands(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('announcements:publish-scheduled')
                ->everyMinute()
                ->name('announcements:publish-scheduled')
                ->withoutOverlapping();
        });
    }

    /**
     * Initialize default settings.
     */
    protected function initializeSettings(): void
    {
        /** @var SettingsService $settings */
        $settings = $this->app->make('settings');
        
        $settings->setDefaultValue('announcements_enabled', true);
        $settings->setDefaultValue('announcements_likes_enabled', true);
        $settings->setDefaultValue('announcements_show_views', true);
        $settings->setDefaultValue('announcements_show_author', true);
        $settings->setDefaultValue('announcements_show_date', true);
        $settings->setDefaultValue('announcements_show_featured', true);
        $settings->setDefaultValue('announcements_scheduling_enabled', true);
        $settings->setDefaultValue('announcements_per_page', 12);
        $settings->setDefaultValue('announcements_default_order', 'featured_position_date');
        $settings->setDefaultValue('announcements_seo_title_template', '{title} - {site_name}');
        $settings->setDefaultValue('announcements_view_mode', 'unique');
        $settings->setDefaultValue('announcements_view_window', 30);
        $settings->setDefaultValue('announcements_anonymous_name', 'Staff');
        $settings->setDefaultValue('announcements_public_url', 'announcements');
        $settings->setDefaultValue('announcements_likes_mode', 'all');
        $settings->setDefaultValue('announcements_rss_enabled', true);
        $settings->setDefaultValue('announcements_rss_limit', 20);
    }

    /**
     * Register settings items in admin panel.
     */
    protected function registerSettingsItems(): void
    {
        /** @var SettingsService $settings */
        $settings = $this->app->make('settings');
        
        // Create dedicated "Announcements" card in admin settings (same pattern as store, helpdesk, etc.)
        $settings->addCard(
            'announcements',
            'announcements::messages.admin.title',
            'announcements::messages.admin.description',
            50 // Order (after other cards)
        );
        
        // Add items to the announcements card
        $settings->addCardItem(
            'announcements',
            'announcements_list',
            'announcements::messages.admin.title',
            'announcements::messages.admin.description',
            'bi bi-megaphone',
            [AnnouncementController::class, 'index'],
            Permission::MANAGE_EXTENSIONS
        );
        
        $settings->addCardItem(
            'announcements',
            'announcement_categories',
            'announcements::messages.categories.title',
            'announcements::messages.categories.description',
            'bi bi-folder',
            [CategoryController::class, 'index'],
            Permission::MANAGE_EXTENSIONS
        );
        
        $settings->addCardItem(
            'announcements',
            'announcements_settings',
            'announcements::messages.settings.title',
            'announcements::messages.settings.description',
            'bi bi-gear',
            [SettingsController::class, 'index'],
            Permission::MANAGE_EXTENSIONS
        );
    }

    /**
     * Register dashboard widget.
     */
    protected function registerDashboardWidget(): void
    {
        $widget = new AdminCountWidget(
            'announcements',
            'bi bi-megaphone',
            'announcements::messages.admin.widget_title',
            function () {
                return Announcement::where('status', 'published')
                    ->where('published_at', '<=', now())
                    ->count();
            },
            Permission::MANAGE_EXTENSIONS
        );
        
        $this->app['extension']->addAdminCountWidget($widget);
    }

    /**
     * Register Blade directives and components.
     */
    protected function registerBladeDirectives(): void
    {
        // Latest announcements directive
        Blade::directive('announcementsLatest', function ($expression) {
            return "<?php echo view('announcements::components.latest', array_merge(['limit' => 5, 'category' => null, 'featuredOnly' => false], $expression ? (is_array($expression) ? $expression : []) : []))->render(); ?>";
        });
        
        // Register component
        Blade::component('announcements-latest', \App\Addons\Announcements\View\Components\LatestAnnouncements::class);
    }
}
