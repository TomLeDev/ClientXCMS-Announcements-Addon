<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

use App\Addons\Announcements\Http\Controllers\Admin\AnnouncementController;
use App\Addons\Announcements\Http\Controllers\Admin\CategoryController;
use App\Addons\Announcements\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

// Announcements management
Route::resource('announcements', AnnouncementController::class)->except(['edit']);
Route::post('announcements/{announcement}/duplicate', [AnnouncementController::class, 'duplicate'])->name('announcements.duplicate');
Route::post('announcements/{announcement}/toggle-publish', [AnnouncementController::class, 'togglePublish'])->name('announcements.toggle-publish');
Route::get('announcements/{announcement}/stats', [AnnouncementController::class, 'stats'])->name('announcements.stats');
Route::get('announcements/{announcement}/export-stats', [AnnouncementController::class, 'exportStats'])->name('announcements.export-stats');
Route::get('announcements/{announcement}/preview', [AnnouncementController::class, 'preview'])->name('announcements.preview');
Route::delete('announcements/{announcement}/cover-image', [AnnouncementController::class, 'removeCoverImage'])->name('announcements.remove-cover');
Route::get('announcements-reorder', [AnnouncementController::class, 'reorder'])->name('announcements.reorder');
Route::post('announcements-positions', [AnnouncementController::class, 'updatePositions'])->name('announcements.positions');

// Categories management
Route::resource('announcement-categories', CategoryController::class)->except(['edit'])->parameters([
    'announcement-categories' => 'category'
]);
Route::get('announcement-categories-reorder', [CategoryController::class, 'reorder'])->name('announcement-categories.reorder');
Route::post('announcement-categories-positions', [CategoryController::class, 'updatePositions'])->name('announcement-categories.positions');

// Settings
Route::get('announcements-settings', [SettingsController::class, 'index'])->name('announcements-settings.index');
Route::post('announcements-settings', [SettingsController::class, 'update'])->name('announcements-settings.update');
Route::post('announcements-settings/test-discord', [SettingsController::class, 'testDiscord'])->name('announcements-settings.test-discord');
