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

/**
 * @OA\Schema(
 *     schema="AnnouncementCategory",
 *     title="Announcement Category",
 *     description="Category for announcements/blog posts",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="News"),
 *     @OA\Property(property="slug", type="string", example="news"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Latest news and updates"),
 *     @OA\Property(property="color", type="string", example="#3B82F6"),
 *     @OA\Property(property="icon", type="string", nullable=true, example="bi bi-newspaper"),
 *     @OA\Property(property="position", type="integer", example=0),
 *     @OA\Property(property="announcements_count", type="integer", example=5),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="url", type="string", example="/announcements?category=news")
 * )
 *
 * @OA\Schema(
 *     schema="AnnouncementCategoryRequest",
 *     title="Announcement Category Request",
 *     description="Request body for creating/updating a category",
 *     required={"name"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="News"),
 *     @OA\Property(property="slug", type="string", maxLength=255, nullable=true, example="news"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Latest news and updates"),
 *     @OA\Property(property="color", type="string", maxLength=7, nullable=true, example="#3B82F6", pattern="^#[0-9A-Fa-f]{6}$"),
 *     @OA\Property(property="icon", type="string", maxLength=50, nullable=true, example="bi bi-newspaper"),
 *     @OA\Property(property="position", type="integer", nullable=true, example=0)
 * )
 */
class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'position' => $this->position,
            'announcements_count' => $this->announcements_count ?? $this->announcements()->count(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'url' => route('announcements.index', ['category' => $this->slug]),
        ];
    }
}
