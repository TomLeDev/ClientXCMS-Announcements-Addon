<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Controllers\Api;

use App\Addons\Announcements\Http\Requests\AnnouncementRequest;
use App\Addons\Announcements\Http\Resources\AnnouncementCollection;
use App\Addons\Announcements\Http\Resources\AnnouncementResource;
use App\Addons\Announcements\Models\Announcement;
use App\Http\Controllers\Api\AbstractApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Announcements",
 *     description="API Endpoints for managing announcements/blog posts"
 * )
 */
class AnnouncementApiController extends AbstractApiController
{
    protected string $model = Announcement::class;

    protected int $perPage = 15;

    protected array $sorts = [
        'id',
        'title',
        'slug',
        'status',
        'featured',
        'position',
        'published_at',
        'created_at',
        'views_count',
        'likes_count',
    ];

    protected array $filters = [
        'id',
        'title',
        'slug',
        'status',
        'featured',
        'category_id',
        'author_id',
    ];

    protected array $relations = [
        'category',
        'author',
    ];

    /**
     * @OA\Get(
     *     path="/application/announcements",
     *     operationId="getAnnouncementsList",
     *     tags={"Announcements"},
     *     summary="Get list of announcements",
     *     description="Returns paginated list of announcements with optional filters, sorting and relations.",
     *     @OA\Parameter(
     *         name="filter[status]",
     *         in="query",
     *         description="Filter by status (draft, published, scheduled, archived)",
     *         @OA\Schema(type="string", enum={"draft", "published", "scheduled", "archived"})
     *     ),
     *     @OA\Parameter(
     *         name="filter[category_id]",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="filter[featured]",
     *         in="query",
     *         description="Filter by featured status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field (prefix with - for descending)",
     *         @OA\Schema(type="string", enum={"id", "title", "published_at", "views_count", "likes_count", "-id", "-title", "-published_at", "-views_count", "-likes_count"})
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include relations (category, author)",
     *         @OA\Schema(type="string", example="category,author")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Announcement")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request): AnnouncementCollection
    {
        $query = QueryBuilder::for(Announcement::class)
            ->allowedFilters([
                'id',
                'title',
                'slug',
                'status',
                'featured',
                'category_id',
                'author_id',
                AllowedFilter::scope('published'),
            ])
            ->allowedSorts($this->sorts)
            ->allowedIncludes($this->relations)
            ->paginate($request->input('per_page', $this->perPage))
            ->appends(request()->query());

        return new AnnouncementCollection($query);
    }

    /**
     * @OA\Get(
     *     path="/application/announcements/published",
     *     operationId="getPublishedAnnouncements",
     *     tags={"Announcements"},
     *     summary="Get only published announcements",
     *     description="Returns paginated list of published announcements (public endpoint).",
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function published(Request $request): AnnouncementCollection
    {
        $query = Announcement::published()
            ->with(['category', 'author'])
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('featured', 'desc')
            ->orderBy('position', 'asc')
            ->orderBy('published_at', 'desc')
            ->paginate($request->input('per_page', $this->perPage));

        return new AnnouncementCollection($query);
    }

    /**
     * @OA\Post(
     *     path="/application/announcements",
     *     operationId="storeAnnouncement",
     *     tags={"Announcements"},
     *     summary="Create a new announcement",
     *     description="Creates a new announcement/blog post.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Announcement created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:announcements,slug',
            'excerpt' => 'nullable|string|max:500',
            'editor_mode' => 'required|in:markdown,html',
            'content_markdown' => 'nullable|string',
            'content_html' => 'nullable|string',
            'status' => 'required|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'featured' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:announcement_categories,id',
            'show_author' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'robots' => 'nullable|string|max:50',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set author from API token owner or default
        $validated['author_id'] = $request->user()?->id ?? auth('admin')->id();

        // Set published_at for immediate publishing
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $announcement = Announcement::create($validated);

        return response()->json(new AnnouncementResource($announcement), 201);
    }

    /**
     * @OA\Get(
     *     path="/application/announcements/{id}",
     *     operationId="getAnnouncementById",
     *     tags={"Announcements"},
     *     summary="Get announcement by ID",
     *     description="Returns a single announcement.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Announcement ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="include",
     *         in="query",
     *         description="Include relations (category, author)",
     *         @OA\Schema(type="string", example="category,author")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $announcement = QueryBuilder::for(Announcement::class)
            ->allowedIncludes($this->relations)
            ->findOrFail($id);

        return response()->json(new AnnouncementResource($announcement));
    }

    /**
     * @OA\Get(
     *     path="/application/announcements/slug/{slug}",
     *     operationId="getAnnouncementBySlug",
     *     tags={"Announcements"},
     *     summary="Get announcement by slug",
     *     description="Returns a single announcement by its slug.",
     *     @OA\Parameter(
     *         name="slug",
     *         in="path",
     *         required=true,
     *         description="Announcement slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function showBySlug(string $slug): JsonResponse
    {
        $announcement = Announcement::with(['category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json(new AnnouncementResource($announcement));
    }

    /**
     * @OA\Post(
     *     path="/application/announcements/{id}",
     *     operationId="updateAnnouncement",
     *     tags={"Announcements"},
     *     summary="Update an announcement",
     *     description="Updates an existing announcement.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Announcement ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement updated",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function update(Request $request, Announcement $announcement): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:announcements,slug,' . $announcement->id,
            'excerpt' => 'nullable|string|max:500',
            'editor_mode' => 'sometimes|in:markdown,html',
            'content_markdown' => 'nullable|string',
            'content_html' => 'nullable|string',
            'status' => 'sometimes|in:draft,published,scheduled,archived',
            'published_at' => 'nullable|date',
            'featured' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'category_id' => 'nullable|exists:announcement_categories,id',
            'show_author' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'robots' => 'nullable|string|max:50',
        ]);

        // Handle published_at for immediate publishing
        if (isset($validated['status']) && $validated['status'] === 'published' && empty($validated['published_at']) && !$announcement->published_at) {
            $validated['published_at'] = now();
        }

        $announcement->update($validated);

        return response()->json(new AnnouncementResource($announcement->fresh()));
    }

    /**
     * @OA\Delete(
     *     path="/application/announcements/{id}",
     *     operationId="deleteAnnouncement",
     *     tags={"Announcements"},
     *     summary="Delete an announcement",
     *     description="Soft deletes an announcement.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Announcement ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement deleted",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function destroy(Announcement $announcement): JsonResponse
    {
        // Delete cover image if exists
        if ($announcement->cover_image && \Storage::exists('public/' . $announcement->cover_image)) {
            \Storage::delete('public/' . $announcement->cover_image);
        }

        $announcement->delete();

        return response()->json(new AnnouncementResource($announcement));
    }

    /**
     * @OA\Post(
     *     path="/application/announcements/{id}/publish",
     *     operationId="publishAnnouncement",
     *     tags={"Announcements"},
     *     summary="Publish an announcement",
     *     description="Publishes a draft announcement immediately.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Announcement ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement published",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function publish(Announcement $announcement): JsonResponse
    {
        $announcement->update([
            'status' => 'published',
            'published_at' => $announcement->published_at ?? now(),
        ]);

        return response()->json(new AnnouncementResource($announcement->fresh()));
    }

    /**
     * @OA\Post(
     *     path="/application/announcements/{id}/unpublish",
     *     operationId="unpublishAnnouncement",
     *     tags={"Announcements"},
     *     summary="Unpublish an announcement",
     *     description="Changes an announcement status to draft.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Announcement ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Announcement unpublished",
     *         @OA\JsonContent(ref="#/components/schemas/Announcement")
     *     ),
     *     @OA\Response(response=404, description="Announcement not found")
     * )
     */
    public function unpublish(Announcement $announcement): JsonResponse
    {
        $announcement->update(['status' => 'draft']);

        return response()->json(new AnnouncementResource($announcement->fresh()));
    }
}
