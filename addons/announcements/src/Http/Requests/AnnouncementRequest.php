<?php

/*
 * This file is part of the CLIENTXCMS project.
 * This file is the property of the CLIENTXCMS association. Any unauthorized use, reproduction, or download is prohibited.
 * For more information, please consult our support: clientxcms.com/client/support.
 * Year: 2024
 */

namespace App\Addons\Announcements\Http\Requests;

use App\Addons\Announcements\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $announcementId = $this->route('announcement')?->id ?? null;
        
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('announcements', 'slug')->ignore($announcementId)->whereNull('deleted_at'),
            ],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'editor_mode' => ['required', Rule::in(['markdown', 'html'])],
            'content_markdown' => ['nullable', 'string'],
            'content_html' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published', 'scheduled', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'featured' => ['nullable'],
            'position' => ['nullable', 'integer', 'min:0'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
            'cover_image_url' => ['nullable', 'string', 'max:2000'],
            'category_id' => ['nullable', 'exists:announcement_categories,id'],
            'show_author' => ['nullable'],
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:160'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:255'],
            'robots' => ['nullable', 'string', 'max:50'],
            'remove_cover_image' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'slug.regex' => __('announcements::messages.validation.slug_format'),
            'slug.unique' => __('announcements::messages.validation.slug_unique'),
            'cover_image.max' => __('announcements::messages.validation.image_too_large'),
            'cover_image.image' => __('announcements::messages.validation.invalid_image'),
            'cover_image.mimes' => __('announcements::messages.validation.invalid_image_format'),
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'featured' => $this->featured == 'true' ? '1' : '0',
            'show_author' => $this->show_author == 'true' ? '1' : '0',
        ]);
    }

    /**
     * Store a new announcement.
     */
    public function store(): Announcement
    {
        $data = [
            'title' => $this->input('title'),
            'slug' => $this->input('slug'),
            'excerpt' => $this->input('excerpt'),
            'editor_mode' => $this->input('editor_mode'),
            'content_markdown' => $this->input('content_markdown'),
            'content_html' => $this->input('content_html'),
            'status' => $this->input('status'),
            'published_at' => $this->input('published_at'),
            'featured' => $this->input('featured'),
            'position' => $this->input('position', 0),
            'category_id' => $this->input('category_id') ?: null,
            'show_author' => $this->input('show_author'),
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'meta_keywords' => $this->input('meta_keywords'),
            'canonical_url' => $this->input('canonical_url'),
            'robots' => $this->input('robots', 'index,follow'),
            'author_id' => auth('admin')->id(),
        ];
        
        // Handle slug
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            $baseSlug = $data['slug'];
            $counter = 1;
            while (Announcement::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $baseSlug . '-' . $counter++;
            }
        }
        
        // Handle published_at and auto-schedule
        if (!empty($data['published_at'])) {
            $publishDate = \Carbon\Carbon::parse($data['published_at']);
            if ($publishDate->isFuture()) {
                $data['status'] = 'scheduled';
            }
        } elseif ($data['status'] === 'published') {
            $data['published_at'] = now();
        }
        
        // Handle cover_image_url
        $coverImageUrl = trim($this->input('cover_image_url', ''));
        if (!empty($coverImageUrl)) {
            $data['cover_image_url'] = $coverImageUrl;
        }
        
        // Create the announcement
        $announcement = Announcement::create($data);
        
        // Handle cover image upload
        if ($this->hasFile('cover_image') && $this->file('cover_image')->isValid()) {
            $filename = $announcement->id . '_cover.' . $this->file('cover_image')->getClientOriginalExtension();
            $this->file('cover_image')->storeAs('public/announcements/covers', $filename);
            $announcement->cover_image = 'announcements/covers/' . $filename;
            $announcement->save();
        }
        
        return $announcement;
    }

    /**
     * Update an existing announcement.
     */
    public function update(): Announcement
    {
        $announcement = $this->route('announcement');
        
        if (!$announcement) {
            throw new \Exception('Announcement not found in route');
        }
        
        // Get cover_image_url from request (can be empty string)
        $coverImageUrl = $this->request->get('cover_image_url', '');
        $coverImageUrl = is_string($coverImageUrl) ? trim($coverImageUrl) : '';
        
        $data = [
            'title' => $this->input('title'),
            'slug' => $this->input('slug') ?: $announcement->slug,
            'excerpt' => $this->input('excerpt'),
            'editor_mode' => $this->input('editor_mode'),
            'content_markdown' => $this->input('content_markdown'),
            'content_html' => $this->input('content_html'),
            'status' => $this->input('status'),
            'published_at' => $this->input('published_at'),
            'featured' => $this->input('featured'),
            'position' => $this->input('position', 0),
            'category_id' => $this->input('category_id') ?: null,
            'show_author' => $this->input('show_author'),
            'meta_title' => $this->input('meta_title'),
            'meta_description' => $this->input('meta_description'),
            'meta_keywords' => $this->input('meta_keywords'),
            'canonical_url' => $this->input('canonical_url'),
            'robots' => $this->input('robots', 'index,follow'),
            // Set cover_image_url directly
            'cover_image_url' => !empty($coverImageUrl) ? $coverImageUrl : null,
        ];
        
        // Handle published_at and auto-schedule
        if (!empty($data['published_at'])) {
            $publishDate = \Carbon\Carbon::parse($data['published_at']);
            if ($publishDate->isFuture()) {
                $data['status'] = 'scheduled';
            }
        } elseif ($data['status'] === 'published' && !$announcement->published_at) {
            $data['published_at'] = now();
        }
        
        // Update the announcement
        $announcement->update($data);
        
        // Handle cover image upload
        if ($this->hasFile('cover_image') && $this->file('cover_image')->isValid()) {
            // Delete old image if exists
            if ($announcement->cover_image != null) {
                \Storage::delete('public/' . $announcement->cover_image);
            }
            $filename = $announcement->id . '_cover.' . $this->file('cover_image')->getClientOriginalExtension();
            $this->file('cover_image')->storeAs('public/announcements/covers', $filename);
            $announcement->cover_image = 'announcements/covers/' . $filename;
            // Clear URL if uploading file
            $announcement->cover_image_url = null;
            $announcement->save();
        }
        
        // Handle remove cover image
        if ($this->remove_cover_image == 'true') {
            if ($announcement->cover_image != null) {
                \Storage::delete('public/' . $announcement->cover_image);
            }
            $announcement->cover_image = null;
            $announcement->save();
        }
        
        return $announcement;
    }
}
