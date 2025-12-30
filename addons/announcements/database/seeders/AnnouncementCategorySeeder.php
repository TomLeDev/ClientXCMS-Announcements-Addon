<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Database\Seeders;

use App\Addons\Announcements\Models\AnnouncementCategory;
use Illuminate\Database\Seeder;

class AnnouncementCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (AnnouncementCategory::count() > 0) {
            return;
        }

        $categories = [
            [
                'name' => 'Actualités',
                'slug' => 'actualites',
                'description' => 'Les dernières actualités et nouveautés',
                'color' => '#3B82F6',
                'icon' => 'bi bi-newspaper',
                'position' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Mises à jour',
                'slug' => 'mises-a-jour',
                'description' => 'Informations sur les mises à jour et améliorations',
                'color' => '#10B981',
                'icon' => 'bi bi-arrow-repeat',
                'position' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance',
                'slug' => 'maintenance',
                'description' => 'Planification et rapports de maintenance',
                'color' => '#F59E0B',
                'icon' => 'bi bi-tools',
                'position' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Promotions',
                'slug' => 'promotions',
                'description' => 'Offres spéciales et promotions',
                'color' => '#EF4444',
                'icon' => 'bi bi-percent',
                'position' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Tutoriels',
                'slug' => 'tutoriels',
                'description' => 'Guides et tutoriels pour nos services',
                'color' => '#8B5CF6',
                'icon' => 'bi bi-book',
                'position' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            AnnouncementCategory::create($category);
        }
    }
}
