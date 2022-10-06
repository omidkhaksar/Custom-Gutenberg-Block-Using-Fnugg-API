<?php

/**
 * Plugin Name:       Fnugg Api
 * Description:       Present data from a ski resort using the Fnugg API
 * Requires at least: 5.9
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Mazaher Khaksar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       fnugg-api
 *
 * @package           create-block
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */


namespace Fnugg;
defined('ABSPATH') || die;

function initialize()
{
    try {
        $autoload = __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


        if (file_exists($autoload)) {
            require_once $autoload;
        }

        // Loading the core modules of the plugin
        $modules = [
            'api'   => (new Api\Api()),
            'block' => (new Block\Block([
                'dir'  => __DIR__,
                'file' => __FILE__,
            ])),
        ];

        return array_map(function ($module) {
            $module->init();
        }, apply_filters('fnugg_core_modules', $modules));
    } catch (\Throwable $throwable) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            throw $throwable;
        }

        do_action('fnugg_error', $throwable);
    }
}
add_action('plugins_loaded', __NAMESPACE__ . '\\initialize');
