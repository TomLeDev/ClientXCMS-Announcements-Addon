<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

use App\Addons\Announcements\Http\Controllers\Front\AnnouncementController;
use Illuminate\Support\Facades\Route;

$publicUrl = setting('announcements_public_url', 'announcements');

Route::prefix($publicUrl)->name('announcements.')->group(function () {
    Route::get('/', [AnnouncementController::class, 'index'])->name('index');
    Route::get('/search', [AnnouncementController::class, 'search'])->name('search');
    Route::get('/rss', [AnnouncementController::class, 'rss'])->name('rss');
    Route::get('/{slug}', [AnnouncementController::class, 'show'])->name('show');
    Route::post('/{slug}/like', [AnnouncementController::class, 'toggleLike'])->name('like');
});
