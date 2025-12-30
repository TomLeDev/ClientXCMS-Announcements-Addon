<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Database\Seeders;

use Illuminate\Database\Seeder;

class AnnouncementsDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AnnouncementCategorySeeder::class,
            AnnouncementSeeder::class,
        ]);
    }
}
