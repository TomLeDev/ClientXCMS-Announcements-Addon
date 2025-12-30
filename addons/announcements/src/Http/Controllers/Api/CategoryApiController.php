<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Controllers\Api;

use App\Addons\Announcements\Http\Resources\CategoryCollection;
use App\Addons\Announcements\Http\Resources\CategoryResource;
use App\Addons\Announcements\Models\AnnouncementCategory;
use App\Http\Controllers\Api\AbstractApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Tag(
 *     name="Announcement Categories",
 *     description="API Endpoints for managing announcement categories"
 * )
 */
class CategoryApiController extends AbstractApiController
{
    protected string $model = AnnouncementCategory::class;

    protected int $perPage = 50;

    protected array $sorts = [
        'id',
        'name',
        'slug',
        'position',
        'created_at',
    ];

    protected array $filters = [
        'id',
        'name',
        'slug',
    ];

    /**
     * @OA\Get(
     *     path="/application/announcements/categories",
     *     operationId="getAnnouncementCategoriesList",
     *     tags={"Announcement Categories"},
     *     summary="Get list of announcement categories",
     *     description="Returns list of announcement categories with optional filters and sorting.",
     *     @OA\Parameter(
     *         name="filter[name]",
     *         in="query",
     *         description="Filter by category name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort field (prefix with - for descending)",
     *         @OA\Schema(type="string", enum={"id", "name", "position", "-id", "-name", "-position"})
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AnnouncementCategory")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden")
     * )
     */
    public function index(Request $request): CategoryCollection
    {
        $query = QueryBuilder::for(AnnouncementCategory::class)
            ->allowedFilters($this->filters)
            ->allowedSorts($this->sorts)
            ->withCount('announcements')
            ->paginate($request->input('per_page', $this->perPage))
            ->appends(request()->query());

        return new CategoryCollection($query);
    }

    /**
     * @OA\Post(
     *     path="/application/announcements/categories",
     *     operationId="storeAnnouncementCategory",
     *     tags={"Announcement Categories"},
     *     summary="Create a new category",
     *     description="Creates a new announcement category.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementCategory")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:announcement_categories,slug',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'position' => 'nullable|integer|min:0',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default color
        if (empty($validated['color'])) {
            $validated['color'] = '#3B82F6';
        }

        $category = AnnouncementCategory::create($validated);

        return response()->json(new CategoryResource($category), 201);
    }

    /**
     * @OA\Get(
     *     path="/application/announcements/categories/{id}",
     *     operationId="getAnnouncementCategoryById",
     *     tags={"Announcement Categories"},
     *     summary="Get category by ID",
     *     description="Returns a single category.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementCategory")
     *     ),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $category = AnnouncementCategory::withCount('announcements')->findOrFail($id);

        return response()->json(new CategoryResource($category));
    }

    /**
     * @OA\Post(
     *     path="/application/announcements/categories/{id}",
     *     operationId="updateAnnouncementCategory",
     *     tags={"Announcement Categories"},
     *     summary="Update a category",
     *     description="Updates an existing category.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementCategoryRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated",
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementCategory")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function update(Request $request, AnnouncementCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:announcement_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'position' => 'nullable|integer|min:0',
        ]);

        $category->update($validated);

        return response()->json(new CategoryResource($category->fresh()));
    }

    /**
     * @OA\Delete(
     *     path="/application/announcements/categories/{id}",
     *     operationId="deleteAnnouncementCategory",
     *     tags={"Announcement Categories"},
     *     summary="Delete a category",
     *     description="Deletes a category. Announcements in this category will have their category set to null.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted",
     *         @OA\JsonContent(ref="#/components/schemas/AnnouncementCategory")
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function destroy(AnnouncementCategory $category): JsonResponse
    {
        // Set category_id to null for all announcements in this category
        $category->announcements()->update(['category_id' => null]);

        $category->delete();

        return response()->json(new CategoryResource($category));
    }
}
