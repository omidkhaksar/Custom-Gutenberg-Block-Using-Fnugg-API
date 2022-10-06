<?php
namespace Fnugg\Block;
defined('ABSPATH') || die;

use Fnugg\Shared\Helpers;

final class Block
{

    protected array $args = [];

    protected string $asset = '';

    public function __construct(array $args)
    {
        $this->args  = $args;
        $this->asset = $this->args['dir']
                     . DIRECTORY_SEPARATOR
                     . 'build';
    }
    
    public function init() : void
    {
        add_action('init', [$this, 'init_block']);
    }

    public function render($atts) : string
    {
        $q = apply_filters('fnugg_frontend_self_api_search_params', ['q' => $atts['resortName']], $atts);

        $transient = Helpers::trans_id($q, get_class($this));

        $response = get_transient($transient);

        if (empty($response)) {
            $response = null;

            /**
             * Filters frontend search API response.
             *
             * @param array $resp
             * @param array $atts
             */
            $response = apply_filters(
                'fnugg_frontend_self_api_search_response',
                Helpers::get_remote_json(
                    add_query_arg(
                        $q,
                        get_rest_url(null, 'fnugg/search/')
                    )
                ),
                $atts
            );

            set_transient($transient, $response, 15 * MINUTE_IN_SECONDS);
        }

        $data = $response;
        $resort = $data['hits']['hits'][0]['_source']['name'];
        $lastUpdate = $data['hits']['hits'][0]['_source']['last_updated'];
        $resortImage = $data['hits']['hits'][0]['_source']['images']['image_16_9_xl'];
        $resortSymbol = $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['symbol']['name'];
        $resortTemperature = $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['temperature'];
        $resortCondition = $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['condition_description'];
        $resortWind = $data['hits']['hits'][0]['_source']['conditions']['combined']['top']['wind'];

        ob_start();
        if ($data){
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
                         <span class="resort-temperature-value"><?php echo $resortTemperature['value'] ?></span>
                         <span class="resort-temperature-measure"><?php echo $resortTemperature['unit'] ?></span>
                     </div>				
                 </div>
                 <div class="resort-sub-details">
                     <div class="resort-condition-description">
                             <span><?php echo $resortCondition ?> </span>
                     </div>
                 </div>
                 <div class="resort-sub-details">
                     <div class="resort-wind">
                         <span class="resort-wind-value"><?php echo $resortWind['mps'] ?> M/S</span>
                     </div>				
                 </div>			
             </div>
         </div>
     
     
         <?php
        }else{
        ?>
        <h1>We Still Caching Please Refresh Again ....</h1>
        <?php
        }
        return ob_get_clean();
    }

    public function init_block() : void
    {
        register_block_type($this->asset, array(
            'render_callback' => [$this, 'render']
        ) );
    }
}
