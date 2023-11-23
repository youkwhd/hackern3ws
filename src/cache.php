<?php

function get_from_inet_or_cache($curl, $memcached, $cache_key, $url_endpoint) {
    if ($memcached && $memcached->get($cache_key))
        return $memcached->get($cache_key);

    curl_setopt($curl, CURLOPT_URL, $url_endpoint);
    $obj = json_decode(curl_exec($curl)) ?: [];

    // cache for 2 minutes
    if ($memcached)
        $memcached->set($cache_key, $obj, time() + 60 * 2);

    return $obj;
}
