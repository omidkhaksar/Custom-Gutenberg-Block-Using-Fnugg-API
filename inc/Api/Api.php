<?php 

namespace Fnugg\Api;
defined('ABSPATH') || die;

use \Fnugg\Data;

final class Api
{
    protected Data\Data $fetch;

    protected string $url;

    public function __construct()
    {
        $this->url = esc_url_raw(
            apply_filters(
                'fnugg_remote_api_url',
                'https://api.fnugg.no/'
            )
        );

        $this->fetch = apply_filters(
            'fnugg_fetch_object',
            new Data\Fetch($this->url),
            $this->url
        );
    }

    public function init() : void
    {
        add_action('rest_api_init', [$this, 'init_rest']);
    }

    public function init_rest() : void
    {
        $routes = [
            'autocomplete' => (new Rest\Autocomplete($this->fetch)),
            'api'          => (new Rest\Search($this->fetch)),
        ];

        $routes = apply_filters('fnugg_rest_routes_init', $routes, $this->fetch, $this->url);

        foreach ($routes as $route) {
            $route->register_routes();
        }
    }
}
