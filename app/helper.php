<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('getImageUrl')) {
    function getImageUrl(?string $path = null): string
    {
        if($path == null) return asset('backend/img/default.jpg');

        return asset($path);
    }
}

if (!function_exists('checkslug')) {
    function checkSlug($table)
    {
        do {
            $slug = substr(md5(mt_rand()), 0, 8);
        } while (DB::table($table)->where('slug', $slug)->exists());

        return $slug;
    }
}




