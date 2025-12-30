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

        // Get admin ID if available
        $adminId = \DB::table('admins')->first()?->id;

        $announcements = [
            [
                'title' => 'Bienvenue sur notre plateforme',
                'slug' => 'bienvenue-sur-notre-plateforme',
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
                'meta_title' => 'Bienvenue sur notre plateforme',
                'meta_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            ],
            [
                'title' => 'Mise à jour v2.0 disponible',
                'slug' => 'mise-a-jour-v2-disponible',
                'excerpt' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Nouveautés de la version 2.0</h2>
<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>

<h3>Nouvelles fonctionnalités</h3>
<ul>
<li>Interface utilisateur modernisée</li>
<li>Amélioration des performances de 50%</li>
<li>Nouveau système de notifications en temps réel</li>
<li>Support multi-langues (FR, EN, ES, DE)</li>
<li>Mode sombre automatique</li>
</ul>

<h3>Corrections</h3>
<p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</p>

<blockquote>
<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit.</p>
</blockquote>

<p><em>L\'équipe technique</em></p>',
                'status' => 'published',
                'featured' => false,
                'position' => 1,
                'category_id' => $mises?->id,
                'author_id' => $adminId,
                'show_author' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Guide de démarrage rapide',
                'slug' => 'guide-de-demarrage-rapide',
                'excerpt' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Premiers pas avec notre plateforme</h2>
<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati.</p>

<h3>Étape 1 : Créer votre compte</h3>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cliquez sur "Inscription" et remplissez le formulaire avec vos informations.</p>

<h3>Étape 2 : Configurer votre profil</h3>
<p>Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Accédez à "Mon compte" pour personnaliser votre expérience.</p>

<h3>Étape 3 : Commander votre premier service</h3>
<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris. Parcourez notre catalogue et sélectionnez le service adapté à vos besoins.</p>

<h3>Besoin d\'aide ?</h3>
<p>Notre équipe support est disponible 24h/24 et 7j/7 pour répondre à toutes vos questions. Excepteur sint occaecat cupidatat non proident.</p>

<p><a href="#">Contacter le support →</a></p>',
                'status' => 'published',
                'featured' => false,
                'position' => 2,
                'category_id' => $tutoriels?->id,
                'author_id' => $adminId,
                'show_author' => true,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Maintenance planifiée ce week-end',
                'slug' => 'maintenance-planifiee-week-end',
                'excerpt' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'editor_mode' => 'html',
                'content_html' => '<h2>Information importante</h2>
<p>Nous vous informons qu\'une maintenance est prévue ce week-end afin d\'améliorer nos infrastructures.</p>

<h3>Détails de la maintenance</h3>
<ul>
<li><strong>Date :</strong> Samedi 15 janvier 2025</li>
<li><strong>Heure :</strong> 02h00 - 06h00 (UTC+1)</li>
<li><strong>Durée estimée :</strong> 4 heures</li>
<li><strong>Services impactés :</strong> Tous les services d\'hébergement</li>
</ul>

<h3>Ce qui va changer</h3>
<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est.</p>

<p>Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.</p>

<p><strong>Merci de votre compréhension.</strong></p>',
                'status' => 'scheduled',
                'featured' => true,
                'position' => 3,
                'category_id' => $actualites?->id,
                'author_id' => $adminId,
                'show_author' => false,
                'published_at' => now()->addDays(2),
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create($data);
        }
    }
}
