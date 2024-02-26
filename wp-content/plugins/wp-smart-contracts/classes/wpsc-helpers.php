<?php

if( ! defined( 'ABSPATH' ) ) die;

/**
 * Include semantic ui JS + CSS + Functions
 */

class WPSC_helpers {

	// use to avoid notices when accesing an array element
	static public function valArrElement($arr=null, $elem=null) {
		if (!is_array($arr)) return false;
		if (!$elem) return false;
		if (!array_key_exists($elem, $arr)) return false;
		return true;
	}

	// get network information from json file
	static function getNetworks() {
	    if (file_exists($json_file = dirname(dirname(__FILE__)).'/assets/json/networks.json') and 
	    	is_array( $arr = json_decode( file_get_contents($json_file), true ) ) ) {
	    	return $arr;
		} else {
			return false;
		}
	}

	// format a number with 18 decimals and the proper number separators
    static public function formatNumber($num) {

    	// convert to float
    	$num = floatval($num);

    	// validate number of decimals to show, use 4 as default
    	if (!is_numeric($ndts = WPSCSettingsPage::numberOfDecimalsToShow())) {
    		$ndts = 4;
    	}

    	// add thousands and decimals separators
        $nf = number_format($num, $ndts, WPSCSettingsPage::numberFormatDecimals(), WPSCSettingsPage::numberFormatThousands());

        return $nf;

    }

	// format a number with 18 decimals and the proper number separators
    static public function formatNumber2($num, $ndts) {

    	// convert to float
    	$num = floatval($num);

    	// add thousands and decimals separators
        $nf = number_format($num, $ndts, WPSCSettingsPage::numberFormatDecimals(), WPSCSettingsPage::numberFormatThousands());

        return $nf;

    }	

    // create a page with QR Scanner and NFT minter if not already created
    static public function createPluginPages() {

    	if (WPSC_assets::getQrScanner()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'WPSC QR Scanner',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_qr_scanner]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_scanner', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    	if (WPSC_assets::getNFTMintPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT Mint',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_mint]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_minter', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    	if (WPSC_assets::getNFTMyItemsPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT My Items',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_my_items]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_my_items', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    	if (WPSC_assets::getNFTAuthorsPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT Authors',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_author]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_author', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

		if (WPSC_assets::getNFTMyBidsPage()===false) {
			$new_page_id = wp_insert_post( array(
	            'post_title'     => 'NFT My Bids',
	            'post_type'      => 'page',
	            'comment_status' => 'closed',
	            'ping_status'    => 'closed',
	            'post_content'   => '[wpsc_nft_my_bids]',
	            'post_status'    => 'publish',
	            'post_author'    => get_user_by( 'id', 1 )->user_id
	        ) );
			if ( $new_page_id && ! is_wp_error( $new_page_id ) ) {
	            update_post_meta( $new_page_id, 'wpsc_is_nft_my_bids', true );
	            update_post_meta( $new_page_id, '_wp_page_template', 'wpsc-clean-template.php' );
	        }
    	}

    }

    // return the short version of an Ethereum address
	public static function shortify($address, $ultra=false) {
		if ($ultra) {
			return substr($address, 0, 4) . '...' . substr($address, -2);
		} else {
			return substr($address, 0, 6) . '...' . substr($address, -4);
		}
	}

	public static function languages() {
		load_plugin_textdomain( 'wp-smart-contracts', false, basename( dirname( __DIR__ ) ) . '/languages/' );
	}

	public static function renderWPICInfo() {
	    $m = new Mustache_Engine;
	    return $m->render(
	      WPSC_Mustache::getTemplate('metabox-wpic-info'), 
	      [
	        "title" => __('Scale Ethereum', 'wp-smart-contracts'),
			"logo" => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/xdai.png',
	        "description_1" => __('Layer 2 Solutions supported by WPSmartContracts', 'wp-smart-contracts'),
	        "description_2" => __('Now you can deploy all smart contracts flavors in four blockchains.', 'wp-smart-contracts'),
	        "feature_1" => __('Ethereum', 'wp-smart-contracts'),
	        "feature_2" => __('xDai Chain', 'wp-smart-contracts'),
	        "feature_3" => __('Binance Smart Chain', 'wp-smart-contracts'),
	        "feature_4" => __('Polygon (previously Matic)', 'wp-smart-contracts'),
	        "feature_5" => __('Easy deploy with lower fees', 'wp-smart-contracts'),
			"blockchain_img" => dirname( plugin_dir_url( __FILE__ )) . '/assets/img/blocks.png',
	        "claim" => __('Claim 5,000 WPIC', 'wp-smart-contracts'),
	        "claim-note" => __('0.1 Ξ', 'wp-smart-contracts'),
	        "learn" => __('Learn more', 'wp-smart-contracts'),
	      ]
	    );
	}

	static public function nativeCoinName($net) {
		$networks = self::getNetworks();
		if (WPSC_helpers::valArrElement($networks, $net) and 
			WPSC_helpers::valArrElement($networks[$net], "coin-symbol") and
			$networks[$net]["coin-symbol"]) {
			return $networks[$net]["coin-symbol"];
		} else {
            return "Ether";
		}
	}

	static public function getIdFromShortcodes() {

		$the_id = get_the_ID();

		$data = get_post_field("post_content", $the_id);

		foreach(['wpsc_coin', 'wpsc_crowdfunding', 'wpsc_ico', 'wpsc_staking'] as $shortcode) {

			preg_match("/\[$shortcode (.+?)\]/", $data, $dat);
		
			$dat = array_pop($dat);
			$dat= explode(" ", $dat);
			$params = array();
			foreach ($dat as $d){
				if ($d) {
					list($opt, $val) = explode("=", $d);
					$params[$opt] = trim($val, '"');	
				}
			}
		
			if (WPSC_helpers::valArrElement($params, "id") and $params["id"]) {
				$the_id = $params["id"];
				break;
			}
		
		}
		
		return $the_id;
	}

	static public function flavors() {
		return ["vanilla", "pistachio", "chocolate", "mango", "raspberry", "bluemoon", "matcha", "mochi", "suika", "ube", "almond"];
	}

	static public function getNetworkInfoJSON($flavor) {

		// store in transient for 6 hours
		$transient_name = WPSC_Endpoints::transientPrefix . "net_info_json";
		$json = false;
		if ($t = get_transient($transient_name)) {
			$json = $t;
		} else {
			if (defined("WPSC_NETINFO_LOCAL_PATH")) {
				$response = wp_remote_get(WPSC_NETINFO_LOCAL_PATH);
			} else {
				$response = wp_remote_get("https://api.wpsmartcontracts.com/netinfo.json");
			}
			
			if ( is_array( $response ) && ! is_wp_error( $response ) ) {
				$json = $response['body'];
				if ($json) {
					json_decode($json);
					if (json_last_error() === JSON_ERROR_NONE) {
						set_transient($transient_name, $json, 21600);
					}
				}
			}
		}
		if ($json) {
			$json = json_decode($json, true);
			if (WPSC_helpers::valArrElement($json, "data")) {
				foreach($json["data"] as $i => $j) {
					if (WPSC_helpers::valArrElement($json["data"][$i], $flavor) and $json["data"][$i][$flavor]) {
						if ($json["data"][$i]["type"]=="Mainnet") {
							$json["data"][$i]["is_mainnet"]=true;
						}
						if (WPSC_helpers::valArrElement($json["data"][$i], "blockchain")) {
							switch($json["data"][$i]["blockchain"]) {
							case "Ethereum":
								$json["data"][$i]["is_ethereum"]=true;
								break;
							case "Bitcoin":
								$json["data"][$i]["is_bitcoin"]=true;
								break;
							}		
						}
						$json["data"][$i]["show"]=true;
						if ($json["data"][$i][$flavor][0]=="Free") {
							$json["data"][$i]["free"]=true;
						} else {
							$json["data"][$i]["fee"]=$json["data"][$i][$flavor][0];
							$json["data"][$i]["fee_usd"]=$json["data"][$i][$flavor][1];
						}
					}
				}
				return ["date_time" => $json["date_time"], "data"=>$json["data"], "asset_path"=>plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/'];    
			}
	   }

	}
	
}

/**
 * Warnings in wṕ-admin
 */

add_action('admin_notices', function () {

	// check Infura settings

	$options = get_option('etherscan_api_key_option');
	$infura_api_key = (WPSC_helpers::valArrElement($options, "infura_api_key") and !empty($options["infura_api_key"]))?$options["infura_api_key"]:false;

	if (empty(trim($infura_api_key))) {
		echo '<div class="notice notice-error is-dismissible">
			<h3>WP Smart Contracts alert - Action needed!</h3><p>To use the WP Smart Contracts plugin properly you need to setup a free <a href="https://infura.io/" target="_blank">Infura Project ID</a> in <a href="'. get_admin_url() . 'options-general.php?page=etherscan-api-key-setting-admin">WP Smart Contracts Settings</a> in your WordPress install</p><p>Otherwise your site may not allow users to properly interact with your Smart Contract if they do not have Metamask installed</p>
		</div>';
	}

	// check that rest api is alive

	$endpoint = get_rest_url(null, 'wpsc/v1/ping');

	$response = wp_remote_get( $endpoint, null );

	if (!WPSC_helpers::valArrElement($response, "response") or !WPSC_helpers::valArrElement($response["response"], "code") or $response["response"]["code"] != "200") {
		
		$get_rest_url = get_rest_url(null, '');

		echo <<<HELP
		<div class="notice notice-error is-dismissible">
			<h3>WP Smart Contracts alert - Action needed!</h3>
			<p>Looks like the <a href="https://developer.wordpress.org/rest-api/" target="_blank">WP Rest API</a> is not working properly in your WordPress installation</p>
			<p>Please check that the URL: <a href="$get_rest_url" target="_blank">$get_rest_url</a> is not failing</p>
			<p>For more information please visit: <a href="https://wordpress.org/support/topic/404-error-on-coin-page-already-created-the-coin-on-mainnet/" target="_blank">our support page</a></p>
		</div>
HELP;
	}

	// NEWS

	if (!WPSC_helpers::valArrElement($_COOKIE, "almond_notice_hide")) {

		$wpsc_logo = dirname( plugin_dir_url( __FILE__ )) . '/assets/img/wpsc-logo.png';
		$wpsc_almond = dirname( plugin_dir_url( __FILE__ )) . '/assets/img/almond-card.png';

		$stake_admin_url = get_admin_url() . "/post-new.php?post_type=staking";

		echo <<<BLOCKCHAIN
		<div id="wpic-notification" class="notice notice-info is-dismissible">
		<p><img src="$wpsc_logo" style="max-width: 300px;"></p>

		<h1>Hello!, thanks for installing WP Smart Contracts v.1.3.1!</h1>	

		<h2>New Flavor ALMOND: Advanced Stakes</h2>
		<p>
			<img src="$wpsc_almond" style="border-radius: 20px;">
		</p>
		» <a href="https://wpsmartcontracts.com/flavors/almond/" target="_blank">Learn more</a></p>

		<h2>Fixes</h2>

		<br>» Auto populate user address on NFT address fields
		<br>» Fix Bootstrap.js conflict with modals
		<br>» Fix nft-authors link
		<br>» Fix Coin shortcode produces error: Wrong Network
		<br>» 3D model support for NFT media in glTF/GLB format
		</p>

		<p style="color: #777;"><input type="checkbox" id="wpic-no-show"> Got it, Thanks!. Please don't show this again.</p>
		<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	 </div>
BLOCKCHAIN;
	}

});

/**
 * Load i18n files
 */
add_action( 'plugins_loaded', ['WPSC_helpers', 'languages'] );


/**
 * One time notification
 */
class OneTimeNotifications {

    public static $_notices  = array();

    public function __construct() {
		add_action( 'shutdown', array( $this, 'save_errors' ) );
    }

    public static function add_notice( $title, $text ) {
        self::$_notices[] = [$title, $text];
    }

    public function save_errors() {
        update_option( 'wpsc_custom_notices', self::$_notices );
    }

    public function output_errors() {
        $errors = maybe_unserialize( get_option( 'wpsc_hide_custom_notices' ) );

        if ( ! empty( $errors ) ) {

            echo '<div id="mc_notices" class="error notice is-dismissible">';

			echo '<h3>WP Smart Contracts change log</h3>';

            foreach ( $errors as $error ) {
				if ($error[0]) echo '<p><b>&raquo; ' . wp_kses_post( $error[0] ) . '</b>';
                if ($error[1]) echo '<br>' . wp_kses_post( $error[1] ) . '</p>';
            }

            echo '</div>';

			update_option( 'wpsc_hide_custom_notices', 1 );

		}
    }

}

if (!get_option( 'wpsc_onetime_notice_1' )) {
	add_action( 'shutdown', function () {
		echo '<div class="notice notice-info is-dismissible">';
		echo '<h3>WP Smart Contracts change log</h3>';
		foreach ([
			["We ♥ WP5.5", "Now WPSmartContracts is compatible with WordPress 5.5"],
			["Uniswap is in da house!", "Now you can add your token to Uniswap and include it in the Exchanges section"],
			["Because good ideas needs your support!", "Starting January 1, 2021 deploy fees may apply, visit <a href=\"https://wpsmartcontracts.com\" target=\"_blank\">wpsmartcontracts.com</a> for more information"]
		] as $notice ) {
			echo '<p><b>&raquo; ' . $notice[0] . '</b>';
			echo '<br>' . $notice[1] . '</p>';
		}
		echo '</div>';
		update_option( 'wpsc_onetime_notice_1', 1 );
	});
}

/*

add_action( 'pre_get_posts', function ( $query ) {

    if (!$query->is_main_query()) {
		echo "NO is_main_query ";
	}
	echo "is_main_query <br>";
	
	if ('' === $query->get( 's' )) {
		echo "query s is empty<br>";
	}

	echo "query s: " . $query->get( 's' ) . "<br>";
	
	if ('coin' !== $query->get( 'post_type' )) {
		echo "Not a coin: " . $query->get( 'post_type' ) . "<br>";
	}

	echo "si coin<br>";

    if (! $query->is_main_query() or '' === $query->get( 's' ) or 'coin' !== $query->get( 'post_type' )) {
        return $query;
	}

	$input = strtolower(sanitize_text_field($query->get( 's' )));

	echo "input: $input<br>";

	$meta_query[] = array(
		'key'     => 'wpsc_flavor',
		'value'   => $input,
		'compare' => '=',
	);

	// Set the meta query to the complete, altered query
	$query->set('meta_query', $meta_query);

	echo "<pre>" . print_r($query, true) . "</pre>"; 

    // Alter whatever you need: Make, Model, etc.
    $query->set( 'meta_query', array(
        'relation' => 'OR',
        array(
            'key'     => 'color',
            'value'   => 'blue',
            'compare' => 'NOT LIKE'
        ),
        array(
            'key'     => 'price',
            'value'   => array( 20, 100 ),
            'type'    => 'numeric',
            'compare' => 'BETWEEN'
        )
    ) );

    return $query;
});

function cf_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'cf_search_join' );

function cf_search_where( $where ) {
    global $pagenow, $wpdb;

    if ( is_search() ) {
        $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
add_filter( 'posts_where', 'cf_search_where' );

function cf_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}
add_filter( 'posts_distinct', 'cf_search_distinct' );

*/

/*
function extend_admin_search( $query ) {

    // Extend search for document post type
    $post_type = ['coin', 'crowdfunding', 'nft-collection', 'staking'];

    // Custom fields to search for
    $custom_fields = array(
        "wpsc_flavor",
    );

    if( ! is_admin() )
        return;

    if ( !in_array($query->query['post_type'], $post_type ) )
        return;

    $search_term = $query->query_vars['s'];

    $query->query_vars['s'] = '';

    if ( $search_term != '' ) {
        $meta_query = array( 'relation' => 'OR' );

        foreach( $custom_fields as $custom_field ) {
            array_push( $meta_query, array(
                'key' => $custom_field,
                'value' => $search_term,
                'compare' => 'LIKE'
            ));
        }

        $query->set( 'meta_query', $meta_query );
    };
}

add_action( 'pre_get_posts', 'extend_admin_search' );

add_action( 'pre_get_posts', function( $q )
{
    if( $title = $q->get( '_meta_or_title' ) )
    {
        add_filter( 'get_meta_sql', function( $sql ) use ( $title )
        {
            global $wpdb;

            // Only run once:
            static $nr = 0; 
            if( 0 != $nr++ ) return $sql;

            // Modify WHERE part:
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare( "{$wpdb->posts}.post_title = '%s'", $title ),
                mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            );
            return $sql;
        });
    }
});

*/

