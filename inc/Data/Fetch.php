<?php

namespace Fnugg\Data;
defined('ABSPATH') || die;

use \Fnugg\Shared\Helpers;

class Fetch implements Data
{

    protected string $url = '';

    public function __construct(string $url)
    {
        $this->url  = untrailingslashit(esc_url_raw($url));
    }

    public function autocomplete(array $q) : array
    {
        $url = add_query_arg($q, $this->url . '/suggest/autocomplete/');
        return Helpers::get_remote_json($url);
    }

    public function search(array $q) : array
    {
        $url = add_query_arg($q, $this->url . '/search/');
        return Helpers::get_remote_json($url);
    }
}
