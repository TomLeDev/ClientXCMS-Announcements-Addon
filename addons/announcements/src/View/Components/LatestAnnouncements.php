<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\View\Components;

use App\Addons\Announcements\Models\Announcement;
use App\Addons\Announcements\Models\AnnouncementCategory;
use Illuminate\View\Component;

class LatestAnnouncements extends Component
{
    public $announcements;
    public $limit;
    public $category;
    public $featuredOnly;
    public $showViewAll;

    /**
     * Create a new component instance.
     */
    public function __construct(
        int $limit = 5,
        ?string $category = null,
        bool $featuredOnly = false,
        bool $showViewAll = true
    ) {
        $this->limit = $limit;
        $this->category = $category;
        $this->featuredOnly = $featuredOnly;
        $this->showViewAll = $showViewAll;
        
        $this->announcements = $this->getAnnouncements();
    }

    /**
     * Get announcements based on parameters.
     */
    protected function getAnnouncements()
    {
        $query = Announcement::published()
            ->with('category')
            ->ordered();
        
        if ($this->featuredOnly) {
            $query->featured();
        }
        
        if ($this->category) {
            $categoryModel = AnnouncementCategory::where('slug', $this->category)->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->id);
            }
        }
        
        return $query->limit($this->limit)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('announcements::components.latest');
    }
}
