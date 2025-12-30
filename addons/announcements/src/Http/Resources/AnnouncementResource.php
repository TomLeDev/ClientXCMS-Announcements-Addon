<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Announcement",
 *     title="Announcement",
 *     description="Announcement/Blog post model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="New Feature Released"),
 *     @OA\Property(property="slug", type="string", example="new-feature-released"),
 *     @OA\Property(property="excerpt", type="string", nullable=true, example="A short description..."),
 *     @OA\Property(property="content_html", type="string", nullable=true, example="<p>Full content here...</p>"),
 *     @OA\Property(property="content_markdown", type="string", nullable=true, example="# Full content here..."),
 *     @OA\Property(property="editor_mode", type="string", enum={"markdown", "html"}, example="html"),
 *     @OA\Property(property="status", type="string", enum={"draft", "published", "scheduled", "archived"}, example="published"),
 *     @OA\Property(property="featured", type="boolean", example=false),
 *     @OA\Property(property="position", type="integer", example=0),
 *     @OA\Property(property="cover_image", type="string", nullable=true, example="announcements/covers/cover_1.jpg"),
 *     @OA\Property(property="cover_image_url", type="string", nullable=true, example="/storage/announcements/covers/cover_1.jpg"),
 *     @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="author_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="show_author", type="boolean", example=true),
 *     @OA\Property(property="views_count", type="integer", example=150),
 *     @OA\Property(property="likes_count", type="integer", example=25),
 *     @OA\Property(property="published_at", type="string", format="date-time", nullable=true, example="2024-12-30T10:00:00Z"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-30T09:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-30T10:00:00Z"),
 *     @OA\Property(property="url", type="string", example="/announcements/new-feature-released"),
 *     @OA\Property(property="meta_title", type="string", nullable=true),
 *     @OA\Property(property="meta_description", type="string", nullable=true),
 *     @OA\Property(property="meta_keywords", type="string", nullable=true),
 *     @OA\Property(property="og_image", type="string", nullable=true),
 *     @OA\Property(property="canonical_url", type="string", nullable=true),
 *     @OA\Property(property="robots", type="string", nullable=true),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         nullable=true,
 *         ref="#/components/schemas/AnnouncementCategory"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="username", type="string")
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="AnnouncementRequest",
 *     title="Announcement Request",
 *     description="Request body for creating/updating an announcement",
 *     required={"title", "editor_mode", "status"},
 *     @OA\Property(property="title", type="string", maxLength=255, example="New Feature Released"),
 *     @OA\Property(property="slug", type="string", maxLength=255, nullable=true, example="new-feature-released"),
 *     @OA\Property(property="excerpt", type="string", maxLength=500, nullable=true, example="A short description..."),
 *     @OA\Property(property="editor_mode", type="string", enum={"markdown", "html"}, example="html"),
 *     @OA\Property(property="content_markdown", type="string", nullable=true),
 *     @OA\Property(property="content_html", type="string", nullable=true),
 *     @OA\Property(property="status", type="string", enum={"draft", "published", "scheduled", "archived"}, example="draft"),
 *     @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="featured", type="boolean", nullable=true, example=false),
 *     @OA\Property(property="position", type="integer", nullable=true, example=0),
 *     @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="show_author", type="boolean", nullable=true, example=true),
 *     @OA\Property(property="meta_title", type="string", maxLength=70, nullable=true),
 *     @OA\Property(property="meta_description", type="string", maxLength=160, nullable=true),
 *     @OA\Property(property="meta_keywords", type="string", maxLength=255, nullable=true),
 *     @OA\Property(property="canonical_url", type="string", format="uri", maxLength=255, nullable=true),
 *     @OA\Property(property="robots", type="string", maxLength=50, nullable=true)
 * )
 */
class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content_html' => $this->content_html,
            'content_markdown' => $this->content_markdown,
            'editor_mode' => $this->editor_mode,
            'status' => $this->status,
            'featured' => (bool) $this->featured,
            'position' => $this->position,
            'cover_image' => $this->cover_image,
            'cover_image_url' => $this->cover_image ? Storage::url($this->cover_image) : null,
            'category_id' => $this->category_id,
            'author_id' => $this->author_id,
            'show_author' => (bool) $this->show_author,
            'views_count' => $this->views_count ?? 0,
            'likes_count' => $this->likes_count ?? 0,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'url' => $this->url,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'og_image' => $this->og_image,
            'og_image_url' => $this->og_image ? Storage::url($this->og_image) : ($this->cover_image ? Storage::url($this->cover_image) : null),
            'canonical_url' => $this->canonical_url,
            'robots' => $this->robots,
            'category' => $this->whenLoaded('category', function () {
                return new CategoryResource($this->category);
            }),
            'author' => $this->whenLoaded('author', function () {
                return [
                    'id' => $this->author->id,
                    'username' => $this->author->username ?? $this->author->name ?? 'Admin',
                ];
            }),
        ];
    }
}
