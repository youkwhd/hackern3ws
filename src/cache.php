<?php

function get_from_inet_or_cache($curl, $memcached, $cache_key, $url_endpoint) {
    if ($memcached && $memcached->get($cache_key))
        return $memcached->get($cache_key);

    curl_setopt($curl, CURLOPT_URL, $url_endpoint);
    $obj = json_decode(curl_exec($curl)) ?: [];

    // Invalid SSL error
    if (curl_errno($curl) == 60) {
        echo curl_error($curl);
        echo "<br/>";
        echo "<br/>";
        echo "You can ignore SSL verification by specifying:";
        echo "<br/>";
        echo "<code>curl_setopt(\$curl, CURLOPT_SSL_VERIFYPEER, false);</code>";
    }

    // cache for 5 minutes
    if ($memcached)
        $memcached->set($cache_key, $obj, time() + 60 * 5);

    return $obj;
}
