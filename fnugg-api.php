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
function create_block_fnugg_api_block_init() {
	register_block_type( __DIR__ . '/build', array(
		'render_callback' => 'render_fnugg_widget'
	) );
}
add_action( 'init', 'create_block_fnugg_api_block_init' );


function render_fnugg_widget($attributes, $content, $block){

	$url = 'https://api.fnugg.no/search';
	$additionalParameters ='&sourceFields=last_updated,name,description,images.image_16_9_xl,conditions.combined';
	$resortName = $attributes['resortName'];

    $fullUrl = $url . '?q=' . $resortName . $additionalParameters ;
	
    $response = wp_remote_get($fullUrl);


	if (is_wp_error($response)) {
		error_log("Error: ". $response->get_error_message());
		return false;
	}

    if ($resortName !== ''){

    $body = wp_remote_retrieve_body($response);

	$data = json_decode($body);



   $resort = $data->hits->hits[0]->_source->name;
   $lastUpdate = $data->hits->hits[0]->_source->last_updated;
   $resortImage = $data->hits->hits[0]->_source->images->image_16_9_xl;
   $resortSymbol = $data->hits->hits[0]->_source->conditions->combined->top->symbol->name;
   $resortTemperature = $data->hits->hits[0]->_source->conditions->combined->top->temperature;
   $resortCondition = $data->hits->hits[0]->_source->conditions->combined->top->condition_description;
   $resortWind = $data->hits->hits[0]->_source->conditions->combined->top->wind;




   ob_start();
    ?>
	<div class="resort-card">
		<div class="badge"><?php echo $resort ?></div>
		<div class="resort-tumb">
			<img src="<?php echo $resortImage ?>" alt="">
		</div>
		<div class="resort-status">
			<span>DAGENS FORHOLD</span>
			<span><?php echo $lastUpdate ?></span>
		</div>
		<div class="resort-details">
			<div class="resort-sub-details">
				<div class="resort-symbol">
					<span><?php echo $resortSymbol ?> </span>
				</div>
			</div>
			<div class="resort-sub-details">
				<div class="resort-temperature">
					<span class="resort-temperature-value"><?php echo $resortTemperature->value ?></span>
					<span class="resort-temperature-measure"><?php echo $resortTemperature->unit ?></span>
				</div>				
			</div>
			<div class="resort-sub-details">
				<div class="resort-condition-description">
						<span><?php echo $resortCondition ?> </span>
				</div>
			</div>
			<div class="resort-sub-details">
				<div class="resort-wind">
					<span class="resort-wind-value"><?php echo $resortWind->mps ?> M/S</span>
					<span class="resort-wind-measure"><?php echo $resortTemperature->speed ?></span>
				</div>				
			</div>			
		</div>
	</div>


    <?php

   return ob_get_clean();
	}
}