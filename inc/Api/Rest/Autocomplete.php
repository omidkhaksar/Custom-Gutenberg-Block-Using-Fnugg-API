<?php

namespace Fnugg\Api\Rest;
defined('ABSPATH') || die;

use \Fnugg\Data\Data;
use \Fnugg\Shared\Helpers;

final class Autocomplete extends \WP_REST_Controller
{

    protected Data $fetch;

    public function __construct(Data $fetch)
    {
        $this->namespace  = 'fnugg';
        $this->rest_base  = 'autocomplete';
        $this->fetch      = $fetch;
    }

    public function register_routes() : void
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => \WP_REST_Server::READABLE,
                    'callback'            => [$this, 'get_items'],
                    'permission_callback' => '__return_true',
                    'args'                => $this->get_collection_params(),
                ],
                'schema' => [$this, 'get_item_schema'],
            ]
        );
    }

    public function get_items($request)
    {
        $q = $request->get_params();

        $q = apply_filters('fnugg_autocomplete_query_args', $q, $request);

        $transient = Helpers::trans_id($q, get_class($this));

        $content = get_transient($transient);

        if (! empty($content)) {
            return $content;
        }

        $content = null;

        $content = apply_filters(
            'fnugg_autocomplete_result',
            $this->fetch->autocomplete($q)['result'],
            $q,
            $request
        );

        set_transient($transient, $content, DAY_IN_SECONDS);

        return $content;
    }
}
