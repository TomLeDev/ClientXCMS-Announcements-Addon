<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $siteName }} - {{ __('announcements::messages.front.title') }}</title>
        <link>{{ $siteUrl }}</link>
        <description>{{ setting('seo_description', '') }}</description>
        <language>{{ app()->getLocale() }}</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>
        <atom:link href="{{ route('announcements.rss') }}" rel="self" type="application/rss+xml"/>
        
        @foreach($announcements as $announcement)
            <item>
                <title><![CDATA[{{ $announcement->title }}]]></title>
                <link>{{ $announcement->url }}</link>
                <description><![CDATA[{{ $announcement->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($announcement->rendered_content), 300) }}]]></description>
                <pubDate>{{ $announcement->published_at?->toRfc2822String() }}</pubDate>
                <guid isPermaLink="true">{{ $announcement->url }}</guid>
                @if($announcement->category)
                    <category><![CDATA[{{ $announcement->category->name }}]]></category>
                @endif
                @if($announcement->cover_image_url)
                    <enclosure url="{{ $announcement->cover_image_url }}" type="image/jpeg"/>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
