<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

return [
    'staff' => 'Équipe',
    'media' => 'Médias',
    'danger_zone' => 'Zone de danger',
    'all' => 'Tous',
    
    'statuses' => [
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'scheduled' => 'Planifié',
        'archived' => 'Archivé',
    ],
    
    'admin' => [
        'title' => 'Annonces',
        'description' => 'Gérez les annonces et actualités de votre site.',
        'widget_title' => 'Annonces publiées',
        'create_title' => 'Créer une annonce',
        'create_subtitle' => 'Créer une nouvelle annonce pour vos visiteurs.',
        'edit_title' => 'Modifier l\'annonce',
        'edit_subtitle' => 'Modifier les informations de l\'annonce.',
        'stats_title' => 'Statistiques de l\'annonce',
        'reorder_title' => 'Réorganiser les annonces',
        'reorder_subtitle' => 'Glissez-déposez pour réorganiser l\'ordre d\'affichage.',
        
        'fields' => [
            'title' => 'Titre',
            'slug' => 'Slug (URL)',
            'excerpt' => 'Extrait',
            'excerpt_help' => 'Court résumé affiché dans les listes.',
            'content' => 'Contenu',
            'editor_mode' => 'Mode éditeur',
            'status' => 'Statut',
            'published_at' => 'Date de publication',
            'featured' => 'Mise en avant',
            'featured_help' => 'Les annonces mises en avant apparaissent en priorité.',
            'position' => 'Position',
            'cover_image' => 'Image de couverture',
            'cover_image_help' => 'Dimensions recommandées : 1200×630 pixels (ratio 1.91:1). Max 5 Mo.',
            'cover_image_url' => 'OU URL de l\'image',
            'cover_image_url_help' => 'Alternative à l\'upload : collez une URL d\'image externe.',
            'og_image_url' => 'OU URL de l\'image OG',
            'og_image_url_help' => 'Alternative à l\'upload : collez une URL d\'image externe.',
            'category' => 'Catégorie',
            'show_author' => 'Afficher l\'auteur',
            'author' => 'Auteur',
            'views' => 'Vues',
            'likes' => 'J\'aime',
        ],
        
        'seo' => [
            'title' => 'SEO',
            'meta_title' => 'Meta titre',
            'meta_title_help' => 'Laissez vide pour utiliser le titre par défaut.',
            'meta_description' => 'Meta description',
            'meta_description_help' => 'Laissez vide pour utiliser l\'extrait.',
            'meta_keywords' => 'Mots-clés',
            'meta_keywords_help' => 'Séparez les mots-clés par des virgules.',
            'og_image' => 'Image OpenGraph',
            'og_image_help' => 'Image affichée lors du partage sur les réseaux sociaux.',
            'og_image_dimensions' => 'Dimensions recommandées : 1200×630 pixels.',
            'canonical_url' => 'URL canonique',
            'robots' => 'Robots',
        ],
        
        'editor_modes' => [
            'markdown' => 'Markdown (WYSIWYG)',
            'html' => 'HTML (Monaco Editor)',
        ],
        
        'actions' => [
            'preview' => 'Prévisualiser',
            'duplicate' => 'Dupliquer',
            'publish' => 'Publier',
            'unpublish' => 'Dépublier',
            'stats' => 'Statistiques',
            'reorder' => 'Réorganiser',
            'remove_cover' => 'Supprimer l\'image',
        ],
        
        'duplicated' => 'Annonce dupliquée avec succès.',
        'published' => 'Annonce publiée avec succès.',
        'unpublished' => 'Annonce dépubliée avec succès.',
        'cover_removed' => 'Image de couverture supprimée.',
        
        'stats' => [
            'total_views' => 'Vues totales',
            'unique_views' => 'Vues uniques',
            'total_likes' => 'J\'aime totaux',
            'views_chart' => 'Évolution des vues',
            'likes_chart' => 'Évolution des j\'aime',
            'top_referers' => 'Principales sources',
            'export_csv' => 'Exporter CSV',
            'period' => 'Période',
            'periods' => [
                '7d' => '7 derniers jours',
                '30d' => '30 derniers jours',
                '90d' => '90 derniers jours',
                '1y' => 'Cette année',
            ],
        ],
    ],
    
    'categories' => [
        'title' => 'Catégories d\'annonces',
        'description' => 'Gérez les catégories pour organiser vos annonces.',
        'create_title' => 'Créer une catégorie',
        'create_subtitle' => 'Créer une nouvelle catégorie d\'annonces.',
        'edit_title' => 'Modifier la catégorie',
        'edit_subtitle' => 'Modifier les informations de la catégorie.',
        'reorder_title' => 'Réorganiser les catégories',
        
        'fields' => [
            'name' => 'Nom',
            'slug' => 'Slug',
            'description' => 'Description',
            'color' => 'Couleur',
            'icon' => 'Icône',
            'position' => 'Position',
            'is_active' => 'Active',
            'announcements_count' => 'Nombre d\'annonces',
        ],
        
        'has_announcements' => 'Cette catégorie contient des annonces et ne peut pas être supprimée.',
    ],
    
    'settings' => [
        'title' => 'Configuration des annonces',
        'description' => 'Paramètres généraux du module annonces.',
        'updated' => 'Paramètres mis à jour avec succès.',
        
        'sections' => [
            'general' => 'Général',
            'display' => 'Affichage',
            'publication' => 'Publication',
            'seo' => 'SEO',
            'tracking' => 'Tracking & Stats',
            'likes' => 'Système de likes',
        ],
        
        'fields' => [
            'enabled' => 'Module actif',
            'enabled_help' => 'Activer ou désactiver le module annonces.',
            'likes_enabled' => 'Activer les likes',
            'likes_enabled_help' => 'Permettre aux visiteurs de liker les annonces.',
            'likes_mode' => 'Mode de likes',
            'likes_mode_help' => 'Définit qui peut liker les annonces.',
            'likes_modes' => [
                'all' => 'Tout le monde (IP + Utilisateurs)',
                'authenticated' => 'Utilisateurs connectés uniquement',
                'ip' => 'Par adresse IP uniquement',
            ],
            'likes_authenticated_only' => 'Likes réservés aux connectés',
            'show_views' => 'Afficher le nombre de vues',
            'show_author' => 'Afficher l\'auteur',
            'show_date' => 'Afficher la date',
            'show_featured' => 'Mettre en avant les annonces vedettes',
            'scheduling_enabled' => 'Activer la planification',
            'per_page' => 'Annonces par page',
            'default_order' => 'Tri par défaut',
            'default_orders' => [
                'featured_position_date' => 'Vedettes > Position > Date',
                'position_date' => 'Position > Date',
                'date' => 'Date uniquement',
            ],
            'seo_title_template' => 'Modèle de titre SEO',
            'seo_title_template_help' => 'Variables: {title}, {site_name}',
            'default_meta_description' => 'Meta description par défaut',
            'default_og_image' => 'Image OG par défaut (URL)',
            'view_mode' => 'Mode de comptage des vues',
            'view_modes' => [
                'total' => 'Total simple',
                'unique' => 'Visiteurs uniques (IP)',
                'authenticated' => 'Utilisateurs connectés uniquement',
            ],
            'view_window' => 'Fenêtre anti-refresh (minutes)',
            'view_window_help' => 'Temps minimum entre deux vues du même visiteur.',
            'anonymous_name' => 'Nom pour auteur anonyme',
            'public_url' => 'URL publique',
            'public_url_help' => 'URL de base (ex: announcements, news, blog).',
        ],
        
        'global_stats' => [
            'title' => 'Statistiques globales (30 derniers jours)',
            'total_announcements' => 'Total annonces',
            'published_announcements' => 'Annonces publiées',
            'total_views' => 'Vues totales',
            'total_likes' => 'Likes totaux',
            'most_viewed' => 'Plus vues',
            'most_liked' => 'Plus aimées',
        ],
    ],
    
    'front' => [
        'title' => 'Annonces',
        'all_announcements' => 'Toutes les annonces',
        'search_placeholder' => 'Rechercher une annonce...',
        'no_results' => 'Aucune annonce trouvée.',
        'load_more' => 'Charger plus',
        'back_to_list' => 'Retour aux annonces',
        'share' => 'Partager',
        'views' => '{0} :count vue|{1} :count vue|[2,*] :count vues',
        'likes' => 'j\'aime',
        'published_on' => 'Publié le',
        'by' => 'par',
        'related' => 'Annonces similaires',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'all_categories' => 'Toutes les catégories',
        'featured' => 'À la une',
        'read_more' => 'Lire la suite',
        'no_content' => 'Aucun contenu disponible.',
        'likes_disabled' => 'Les likes sont désactivés.',
        'login_required' => 'Vous devez être connecté pour liker cette annonce.',
        'like_added' => 'Vous avez aimé cette annonce.',
        'like_removed' => 'Vous n\'aimez plus cette annonce.',
        'login_redirect' => 'Voulez-vous vous connecter ?',
        'link_copied' => 'Lien copié dans le presse-papiers !',
        'copy_link' => 'Copiez ce lien :',
    ],
    
    'validation' => [
        'slug_format' => 'Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets.',
        'slug_unique' => 'Ce slug est déjà utilisé.',
        'image_too_large' => 'L\'image ne doit pas dépasser 5 Mo.',
        'invalid_image' => 'Le fichier doit être une image valide.',
        'invalid_image_format' => 'Format d\'image non supporté. Utilisez : JPEG, PNG, GIF ou WebP.',
        'color_format' => 'La couleur doit être au format hexadécimal (#RRGGBB).',
    ],
    
    'api' => [
        'announcements' => 'Annonces',
        'categories' => 'Catégories d\'annonces',
        'not_found' => 'Annonce introuvable.',
        'category_not_found' => 'Catégorie introuvable.',
        'created' => 'Annonce créée avec succès.',
        'updated' => 'Annonce mise à jour avec succès.',
        'deleted' => 'Annonce supprimée avec succès.',
        'published' => 'Annonce publiée avec succès.',
        'unpublished' => 'Annonce dépubliée avec succès.',
    ],
    
    'components' => [
        'latest' => [
            'title' => 'Dernières annonces',
            'view_all' => 'Voir toutes les annonces',
            'no_announcements' => 'Aucune annonce pour le moment.',
        ],
    ],
];
