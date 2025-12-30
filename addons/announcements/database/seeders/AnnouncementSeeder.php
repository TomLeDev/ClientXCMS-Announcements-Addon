<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Database\Seeders;

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
        if (Announcement::count() > 0) {
            return;
        }

        // Get categories
        $actualites = AnnouncementCategory::where('slug', 'actualites')->first();
        $mises = AnnouncementCategory::where('slug', 'mises-a-jour')->first();
        $tutoriels = AnnouncementCategory::where('slug', 'tutoriels')->first();

        $announcements = [
            [
                'title' => 'Bienvenue sur notre plateforme !',
                'slug' => 'bienvenue-sur-notre-plateforme',
                'excerpt' => 'Découvrez toutes les fonctionnalités de notre nouvelle plateforme et comment en tirer le meilleur parti.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Bienvenue !</h2>
<p>Nous sommes ravis de vous accueillir sur notre plateforme. Cette section annonces vous permettra de rester informé de toutes nos actualités.</p>
<h3>Ce que vous trouverez ici</h3>
<ul>
<li><strong>Actualités</strong> - Les dernières nouvelles concernant nos services</li>
<li><strong>Mises à jour</strong> - Informations sur les nouvelles fonctionnalités</li>
<li><strong>Maintenance</strong> - Calendrier des maintenances planifiées</li>
<li><strong>Promotions</strong> - Offres spéciales et réductions</li>
</ul>
<p>N\'hésitez pas à consulter régulièrement cette section pour ne rien manquer !</p>',
                'status' => 'published',
                'featured' => true,
                'position' => 0,
                'category_id' => $actualites?->id,
                'show_author' => true,
                'published_at' => now()->subDays(7),
            ],
            [
                'title' => 'Nouvelle version disponible - v2.0',
                'slug' => 'nouvelle-version-disponible-v2',
                'excerpt' => 'La version 2.0 est maintenant disponible avec de nombreuses améliorations et nouvelles fonctionnalités.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Quoi de neuf dans la v2.0 ?</h2>
<p>Nous avons le plaisir de vous annoncer la sortie de la version 2.0 de notre plateforme.</p>
<h3>Nouvelles fonctionnalités</h3>
<ul>
<li>Interface utilisateur modernisée</li>
<li>Amélioration des performances</li>
<li>Nouveau système de notifications</li>
<li>Support multi-langues amélioré</li>
</ul>
<h3>Corrections de bugs</h3>
<p>Cette mise à jour corrige également plusieurs bugs signalés par notre communauté. Merci pour vos retours !</p>
<p><em>L\'équipe technique</em></p>',
                'status' => 'published',
                'featured' => false,
                'position' => 1,
                'category_id' => $mises?->id,
                'show_author' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Guide de démarrage rapide',
                'slug' => 'guide-de-demarrage-rapide',
                'excerpt' => 'Apprenez à utiliser notre plateforme en quelques minutes avec ce guide complet pour débutants.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Premiers pas</h2>
<p>Ce guide vous aidera à prendre en main rapidement notre plateforme.</p>
<h3>Étape 1 : Créer votre compte</h3>
<p>Si ce n\'est pas déjà fait, commencez par créer votre compte en cliquant sur "Inscription".</p>
<h3>Étape 2 : Configurer votre profil</h3>
<p>Complétez vos informations personnelles dans la section "Mon compte".</p>
<h3>Étape 3 : Explorer les services</h3>
<p>Parcourez notre catalogue de services et trouvez celui qui correspond à vos besoins.</p>
<h3>Besoin d\'aide ?</h3>
<p>Notre équipe support est disponible 24/7 pour répondre à vos questions. N\'hésitez pas à nous contacter !</p>',
                'status' => 'published',
                'featured' => false,
                'position' => 2,
                'category_id' => $tutoriels?->id,
                'show_author' => true,
                'published_at' => now()->subDay(),
            ],
        ];

        foreach ($announcements as $data) {
            // Get admin ID if available
            $adminId = \DB::table('admins')->first()?->id;
            $data['author_id'] = $adminId;
            
            Announcement::create($data);
        }
    }
}
