<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This addon is the property of the CLIENTXCMS association.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

use App\Addons\Announcements\Http\Controllers\Api\AnnouncementApiController;
use App\Addons\Announcements\Http\Controllers\Api\CategoryApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Announcements API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the AnnouncementsServiceProvider and are
| prefixed with 'api/application' and protected by Sanctum authentication.
|
*/

// Public endpoint for published announcements (requires minimal permission)
Route::middleware(['ability:announcements:index,*'])
    ->get('/announcements/published', [AnnouncementApiController::class, 'published'])
    ->name('announcements.published');

// Get announcement by slug (public)
Route::middleware(['ability:announcements:show,*'])
    ->get('/announcements/slug/{slug}', [AnnouncementApiController::class, 'showBySlug'])
    ->name('announcements.show.slug');

// Announcements CRUD
Route::middleware(['ability:announcements:index,*'])
    ->get('/announcements', [AnnouncementApiController::class, 'index'])
    ->name('announcements.index');

Route::middleware(['ability:announcements:store,*'])
    ->post('/announcements', [AnnouncementApiController::class, 'store'])
    ->name('announcements.store');

Route::middleware(['ability:announcements:show,*'])
    ->get('/announcements/{announcement}', [AnnouncementApiController::class, 'show'])
    ->name('announcements.show');

Route::middleware(['ability:announcements:update,*'])
    ->post('/announcements/{announcement}', [AnnouncementApiController::class, 'update'])
    ->name('announcements.update');

Route::middleware(['ability:announcements:delete,*'])
    ->delete('/announcements/{announcement}', [AnnouncementApiController::class, 'destroy'])
    ->name('announcements.delete');

// Announcement actions
Route::middleware(['ability:announcements:update,*'])
    ->post('/announcements/{announcement}/publish', [AnnouncementApiController::class, 'publish'])
    ->name('announcements.publish');

Route::middleware(['ability:announcements:update,*'])
    ->post('/announcements/{announcement}/unpublish', [AnnouncementApiController::class, 'unpublish'])
    ->name('announcements.unpublish');

// Categories CRUD
Route::middleware(['ability:announcements:index,*'])
    ->get('/announcements/categories', [CategoryApiController::class, 'index'])
    ->name('announcements.categories.index');

Route::middleware(['ability:announcements:store,*'])
    ->post('/announcements/categories', [CategoryApiController::class, 'store'])
    ->name('announcements.categories.store');

Route::middleware(['ability:announcements:show,*'])
    ->get('/announcements/categories/{category}', [CategoryApiController::class, 'show'])
    ->name('announcements.categories.show');

Route::middleware(['ability:announcements:update,*'])
    ->post('/announcements/categories/{category}', [CategoryApiController::class, 'update'])
    ->name('announcements.categories.update');

Route::middleware(['ability:announcements:delete,*'])
    ->delete('/announcements/categories/{category}', [CategoryApiController::class, 'destroy'])
    ->name('announcements.categories.delete');
