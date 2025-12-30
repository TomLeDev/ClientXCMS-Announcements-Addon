<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Models;

use App\Models\Account\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $announcement_id
 * @property int|null $user_id
 * @property string|null $ip_hash
 * @property string|null $cookie_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Announcement $announcement
 * @property-read Customer|null $user
 */
class AnnouncementLike extends Model
{
    protected $table = 'announcement_likes';

    protected $fillable = [
        'announcement_id',
        'user_id',
        'ip_hash',
        'cookie_id',
    ];

    /**
     * Get the announcement.
     */
    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class, 'announcement_id');
    }

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
