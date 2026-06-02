<?php

if (! function_exists('status_label')) {
    function status_label(string $status): string
    {
        $badges = [
            'available' => '<span class="badge bg-success">Available</span>',
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'suspend' => '<span class="badge bg-danger">Suspended</span>',
            'borrowed' => '<span class="badge bg-warning">Borrowed</span>',
            'reserved' => '<span class="badge bg-info">Reserved</span>',
            'maintenance' => '<span class="badge bg-secondary">Maintenance</span>',
            'damaged' => '<span class="badge bg-danger">Damaged</span>',
            'lost' => '<span class="badge bg-dark">Lost</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}

if(! function_exists('status_badge')) {
    function status_badge(string $status, bool $is_success = false): string
    {
        $badges = [
            true => "<span class='badge bg-success'>$status</span>",
            false => "<span class='badge bg-danger'>$status</span>"
        ];

        return $badges[$is_success] ?? "<span class='badge bg-secondary'>$status</span>";
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
        $src = $url ? asset('storage/' . $url) : asset('assets/img/profile/no_profile.png');
        $alt = $url ? $title : 'No Profile';

        return '<img src="' . e($src) . '" alt="' . e($alt) . '" class="avatar-img rounded" />';
    }
}

if (! function_exists('format_currency')) {
    function format_currency($amount, string $currency ='USD '): string
    {
        $formatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }
}

if(! function_exists(('edit_button'))) {
    function edit_button(string $url): string
    {
        return "<a href='$url' class='btn btn-sm btn-warning'>
                    <i class='fa fa-edit'></i>
                </a>";
    }
}

if(! function_exists('delete_button')) {
    function delete_button(string $url): string
    {
        return "<form action='$url' method='POST' style='display:inline-block;' onsubmit='return confirm(\"Are you sure you want to delete this item?\")'>
                    " . csrf_field() . method_field('DELETE') . "
                    <button type='submit' class='btn btn-sm btn-danger js-delete-btn'>
                        <i class='fa fa-trash'></i>
                    </button>
                </form>";
    }
}
