<?php 

namespace Fnugg\Shared;
defined('ABSPATH') || die;

final class Helpers
{
    public static function get_remote_json(string $url) : array
    {
        $args = apply_filters('fnugg_wp_remote_get_args', [
            'timeout'             => 10,
            'redirection'         => 0,
            'limit_response_size' => 153600, // 150 KB
        ], $url);

        $result = wp_remote_get($url, $args);
        $result = wp_remote_retrieve_body($result);
        $result = json_decode($result, true);

        if (empty($result)) {
            return [];
        }

        return $result;
    }

    public static function trans_id(array $q, $t) : string
    {
        if (empty($t)) {
            $t = '_';
        }

        return hash('sha256', $t . '_' . http_build_query($q));
    }
}
