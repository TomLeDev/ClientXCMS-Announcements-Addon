<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Console\Commands;

use App\Addons\Announcements\Models\Announcement;
use Illuminate\Console\Command;

class PublishScheduledAnnouncementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled announcements that have reached their publication date';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $announcements = Announcement::where('status', 'scheduled')
            ->where('published_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($announcements as $announcement) {
            $announcement->update(['status' => 'published']);
            $this->info("Published: {$announcement->title}");
            $count++;
        }

        if ($count > 0) {
            $this->info("Successfully published {$count} scheduled announcement(s).");
        } else {
            $this->info('No scheduled announcements to publish.');
        }

        return Command::SUCCESS;
    }
}
