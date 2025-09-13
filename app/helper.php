<?php

use Illuminate\Support\Facades\DB;

 function getImageUrl(string $path = null): string
{
    if($path == null) return asset('backend/img/default.jpg');

    return asset($path);
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



function jimiSign(array $params, string $secret): string
{
    unset($params['sign']);
    ksort($params);

    $query = $secret;
    foreach ($params as $key => $value) {
        if ($value !== null && $value !== '') {
            $query .= $key . $value;
        }
    }
    $query .= $secret;

    return strtoupper(md5($query));
}

