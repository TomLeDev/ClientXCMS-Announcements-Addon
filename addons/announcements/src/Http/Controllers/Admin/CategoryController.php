<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Controllers\Admin;

use App\Addons\Announcements\Http\Requests\CategoryRequest;
use App\Addons\Announcements\Models\AnnouncementCategory;
use App\Http\Controllers\Admin\AbstractCrudController;
use App\Models\Admin\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends AbstractCrudController
{
    protected string $model = AnnouncementCategory::class;
    protected string $viewPath = 'announcements_admin::categories';
    protected string $translatePrefix = 'announcements::messages.categories';
    protected string $routePath = 'admin.announcement-categories';
    protected bool $extensionPermission = true;
    protected int $perPage = 20;
    protected string $searchField = 'name';

    /**
     * Query index with ordering.
     */
    protected function queryIndex(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return AnnouncementCategory::withCount('announcements')
            ->orderBy('position', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($this->perPage)
            ->appends(request()->query());
    }

    /**
     * Store a new category.
     */
    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();
        
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $validated['is_active'] = $request->input('is_active') === 'true' || $request->input('is_active') === true;
        
        $category = AnnouncementCategory::create($validated);
        
        return $this->storeRedirect($category);
    }

    /**
     * Show a category.
     */
    public function show(AnnouncementCategory $category)
    {
        return $this->showView(['item' => $category]);
    }

    /**
     * Update a category.
     */
    public function update(CategoryRequest $request, AnnouncementCategory $category)
    {
        $validated = $request->validated();
        
        $validated['is_active'] = $request->input('is_active') === 'true' || $request->input('is_active') === true;
        
        $category->update($validated);
        
        return $this->updateRedirect($category);
    }

    /**
     * Delete a category.
     */
    public function destroy(AnnouncementCategory $category)
    {
        // Check if category has announcements
        if ($category->announcements()->count() > 0) {
            return back()->with('error', __('announcements::messages.categories.has_announcements'));
        }
        
        $category->delete();
        
        return $this->deleteRedirect($category);
    }

    /**
     * Update positions (AJAX).
     */
    public function updatePositions(Request $request)
    {
        $positions = $request->input('positions', []);
        
        foreach ($positions as $id => $position) {
            AnnouncementCategory::where('id', $id)->update(['position' => $position]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Reorder page.
     */
    public function reorder()
    {
        $categories = AnnouncementCategory::orderBy('position', 'asc')
            ->orderBy('name', 'asc')
            ->get();
        
        return view('announcements_admin::categories.reorder', [
            'items' => $categories,
            'translatePrefix' => $this->translatePrefix,
            'routePath' => $this->routePath,
        ]);
    }
}
