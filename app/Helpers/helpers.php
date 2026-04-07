<?php

if (! function_exists('status_label')) {
    function status_label(string $status): string
    {
        $badges = [
            'available' => '<span class="badge bg-success">Available</span>',
            'borrowed' => '<span class="badge bg-warning">Borrowed</span>',
            'reserved' => '<span class="badge bg-info">Reserved</span>',
            'maintenance' => '<span class="badge bg-secondary">Maintenance</span>',
            'damaged' => '<span class="badge bg-danger">Damaged</span>',
            'lost' => '<span class="badge bg-dark">Lost</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}

if (! function_exists('format_date')) {
    function format_date($date, string $format = 'd-M-Y'): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '-';
    }
}

if (! function_exists('format_date_time')) {
    function format_date_time($date, string $format = 'd-M-Y H:i:s'): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '-';
    }
}

if (! function_exists('format_time')) {
    function format_time($date, string $format = 'H:i A'): string
    {
        return $date ? \Carbon\Carbon::parse($date)->format($format) : '-';
    }
}

if(! function_exists('book_preview')) {
    function book_preview(?string $url, string $title = 'Image Preview'): string
    {
        $src = $url ? asset('storage/' . $url) : asset('assets/img/books/no_cover.jpg');
        $alt = $url ? $title : 'No Cover';

        return '<a href="javascript:void(0)" class="js-image-preview" data-image="' . e($src) . '" data-title="' . e($alt) . '">
                    <img src="' . e($src) . '"
                        alt="' . e($alt) . '"
                        style="width: 50px; height: 60px; object-fit: cover; border-radius: 4px; cursor: zoom-in;">
                </a>';
    }
}

if(! function_exists('image_preview')) {
    function image_preview(?string $url, string $title = 'Image Preview'): string
    {
        $src = $url ? asset('storage/' . $url) : asset('assets/img/books/no_cover.jpg');
        $alt = $url ? $title : 'No Cover';

        return '<img src="' . e($src) . '" alt="' . e($alt) . '" class="avatar-img rounded" />';
    }
}
