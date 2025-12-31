<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace Database\Seeders;

use App\Addons\Announcements\Models\Announcement;
use App\Addons\Announcements\Models\AnnouncementCategory;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Announcement::withTrashed()->count() > 0) {
            return;
        }

        // Get categories
        $actualites = AnnouncementCategory::where('slug', 'actualites')->first();
        $mises = AnnouncementCategory::where('slug', 'mises-a-jour')->first();
        $tutoriels = AnnouncementCategory::where('slug', 'tutoriels')->first();

        // Get admin ID if available
        $adminId = \DB::table('admins')->first()?->id;

        $announcements = [
            [
                'title' => 'Bienvenue sur notre Hébergeur',
                'slug' => 'bienvenue-sur-notre-hebergeur',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Bienvenue !</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

<h3>Nos services</h3>
<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

<ul>
<li><strong>Hébergement Web</strong> - Sed ut perspiciatis unde omnis iste natus error</li>
<li><strong>Serveurs VPS</strong> - Nemo enim ipsam voluptatem quia voluptas sit</li>
<li><strong>Support 24/7</strong> - At vero eos et accusamus et iusto odio dignissimos</li>
</ul>

<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus.</p>',
                'status' => 'published',
                'featured' => true,
                'position' => 0,
                'category_id' => $actualites?->id,
                'author_id' => $adminId,
                'show_author' => true,
                'published_at' => now()->subDays(7),
                'meta_title' => 'Bienvenue sur notre Hébergeur',
                'meta_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create($data);
        }
    }
}
