# Addon Annonces pour ClientXCMS

## Description

Module complet de gestion d'annonces/blog pour ClientXCMS incluant :
- Création/édition d'annonces avec éditeur Markdown WYSIWYG ou HTML Monaco
- Système de catégories avec couleurs et icônes
- Statistiques détaillées (vues, likes, sources)
- SEO complet par annonce
- Système de likes optionnel (authentifié, IP ou les deux)
- Mise en avant et ordre manuel
- Recherche dynamique AJAX
- Flux RSS
- **API REST complète avec documentation Swagger**

## Installation

1. Activer l'extension dans **/admin/settings/extensions/extensions**

Si nécessaire :
2. Exécuter les migrations : `php artisan migrate`
3. Créer le lien symbolique : `php artisan storage:link`

## API REST

L'addon expose une API REST complète, documentée avec Swagger/OpenAPI.

### Authentification

L'API utilise les tokens Sanctum de ClientXCMS. Les permissions sont gérées par les abilities du token.

### Abilities requises

| Endpoint | Ability requise |
|----------|-----------------|
| GET /announcements | `announcements:index` ou `*` |
| POST /announcements | `announcements:store` ou `*` |
| GET /announcements/{id} | `announcements:show` ou `*` |
| POST /announcements/{id} | `announcements:update` ou `*` |
| DELETE /announcements/{id} | `announcements:delete` ou `*` |

### Endpoints des annonces

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/application/announcements` | Liste paginée avec filtres |
| GET | `/api/application/announcements/published` | Annonces publiées uniquement |
| GET | `/api/application/announcements/{id}` | Détail par ID |
| GET | `/api/application/announcements/slug/{slug}` | Détail par slug |
| POST | `/api/application/announcements` | Créer une annonce |
| POST | `/api/application/announcements/{id}` | Mettre à jour |
| DELETE | `/api/application/announcements/{id}` | Supprimer |
| POST | `/api/application/announcements/{id}/publish` | Publier |
| POST | `/api/application/announcements/{id}/unpublish` | Dépublier |

### Endpoints des catégories

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| GET | `/api/application/announcements/categories` | Liste des catégories |
| GET | `/api/application/announcements/categories/{id}` | Détail catégorie |
| POST | `/api/application/announcements/categories` | Créer une catégorie |
| POST | `/api/application/announcements/categories/{id}` | Mettre à jour |
| DELETE | `/api/application/announcements/categories/{id}` | Supprimer |

### Paramètres de filtrage (GET /announcements)

```
?filter[status]=published
?filter[category_id]=1
?filter[featured]=true
?sort=-published_at
?include=category,author
?per_page=15
```

### Exemple de requête

```bash
# Lister les annonces publiées
curl -X GET "https://votresite.com/api/application/announcements/published" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"

# Créer une annonce
curl -X POST "https://votresite.com/api/application/announcements" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Nouvelle annonce",
    "editor_mode": "html",
    "content_html": "<p>Contenu de l annonce</p>",
    "status": "published",
    "category_id": 1
  }'
```

### Réponse type

```json
{
  "data": {
    "id": 1,
    "title": "Nouvelle annonce",
    "slug": "nouvelle-annonce",
    "excerpt": null,
    "content_html": "<p>Contenu de l annonce</p>",
    "status": "published",
    "featured": false,
    "cover_image": "announcements/covers/cover_1.jpg",
    "cover_image_url": "/storage/announcements/covers/cover_1.jpg",
    "views_count": 0,
    "likes_count": 0,
    "published_at": "2024-12-30T10:00:00Z",
    "url": "/announcements/nouvelle-annonce",
    "category": {
      "id": 1,
      "name": "News",
      "slug": "news",
      "color": "#3B82F6"
    }
  }
}
```

## Flux RSS

Le flux RSS est disponible à l'URL `/announcements/rss` et contient les 20 dernières annonces publiées.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
  <channel>
    <title>Annonces - VotreSite</title>
    <link>https://votresite.com/announcements</link>
    <description>Les dernières annonces</description>
    <item>
      <title>Titre de l'annonce</title>
      <link>https://votresite.com/announcements/slug</link>
      <description>Extrait ou contenu</description>
      <pubDate>Mon, 30 Dec 2024 10:00:00 +0000</pubDate>
    </item>
  </channel>
</rss>
```

## Configuration

Accessible via **Admin > Personnalisation > Configuration des annonces** :

### Général
- `announcements_enabled` : Activer/désactiver le module
- `announcements_public_url` : URL de base (défaut: "announcements")

### Affichage
- `announcements_show_views` : Afficher le compteur de vues
- `announcements_show_author` : Afficher l'auteur
- `announcements_show_date` : Afficher la date
- `announcements_show_featured` : Mettre en avant les annonces vedettes
- `announcements_per_page` : Nombre par page (défaut: 12)
- `announcements_default_order` : Tri par défaut
- `announcements_anonymous_name` : Nom pour auteur anonyme

### Likes
- `announcements_likes_enabled` : Activer les likes
- `announcements_likes_mode` : Mode (all/authenticated/ip)

### Tracking
- `announcements_view_mode` : Mode de comptage (total/unique/authentifié)
- `announcements_view_window` : Fenêtre anti-refresh (minutes)
- `announcements_scheduling_enabled` : Activer la planification

### SEO
- `announcements_seo_title_template` : Template de titre
- `announcements_default_meta_description` : Description par défaut
- `announcements_default_og_image` : Image OG par défaut

### RSS
- `announcements_rss_enabled` : Activer le flux RSS
- `announcements_rss_limit` : Nombre d'items (défaut: 20)

## Permissions

L'addon utilise la permission native `admin.manage_extensions` de ClientXCMS pour l'interface admin.

Pour l'API, les abilities sont gérées par les tokens Sanctum :
- `announcements:index` - Lire les annonces
- `announcements:show` - Voir le détail
- `announcements:store` - Créer
- `announcements:update` - Modifier
- `announcements:delete` - Supprimer
- `*` - Accès complet

## Intégration Thème

### Composant Blade

Afficher les dernières annonces dans un thème :

```blade
{{-- Avec le composant --}}
<x-announcements-latest :limit="5" :featuredOnly="false" :showViewAll="true" />

{{-- Ou avec include --}}
@include('announcements::components.latest', [
    'limit' => 5,
    'category' => null,
    'featuredOnly' => false,
    'showViewAll' => true
])
```

### Paramètres disponibles

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| limit | int | 5 | Nombre d'annonces |
| category | string | null | Slug de catégorie |
| featuredOnly | bool | false | Uniquement les mises en avant |
| showViewAll | bool | true | Afficher lien "Voir tout" |

## Routes

### Admin

| Route | Méthode | Description |
|-------|---------|-------------|
| /admin/announcements | GET | Liste des annonces |
| /admin/announcements/create | GET | Formulaire création |
| /admin/announcements/{id} | GET | Édition |
| /admin/announcements/{id}/stats | GET | Statistiques |
| /admin/announcement-categories | GET | Liste catégories |
| /admin/announcements-settings | GET | Configuration |

### Public

| Route | Méthode | Description |
|-------|---------|-------------|
| /announcements | GET | Liste publique |
| /announcements/search | GET | Recherche AJAX |
| /announcements/rss | GET | Flux RSS |
| /announcements/{slug} | GET | Détail annonce |
| /announcements/{slug}/like | POST | Toggle like |

## Modèles de données

### Announcement
- title, slug, excerpt
- editor_mode (markdown/html)
- content_markdown, content_html
- status (draft/published/scheduled/archived)
- published_at, featured, position
- cover_image, category_id, author_id
- SEO: meta_title, meta_description, meta_keywords, og_image, canonical_url, robots
- Stats: views_count, likes_count

### AnnouncementCategory
- name, slug, description
- color (#RRGGBB), icon
- position, is_active

## Changelog

### v1.1.0
- Ajout API REST complète
- Documentation Swagger/OpenAPI
- Amélioration gestion des images
- Trait HandlesAnnouncementImages
- Resources API (AnnouncementResource, CategoryResource)
- Nouvelles traductions API

### v1.0.0
- Version initiale
- Gestion complète des annonces
- Catégories avec couleurs
- Double éditeur Markdown/HTML
- Statistiques et tracking
- Système de likes
- SEO complet
- Recherche dynamique
- Flux RSS
- Composant d'intégration thème
