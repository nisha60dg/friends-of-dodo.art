<?php

if( ! defined( 'ABSPATH' ) ) die;

add_action('admin_menu', function() {
    add_menu_page(__('WPSmartContracts Dashboard'), __('Smart Contract Creation Wizard'), 'edit_posts', 'wpsc_dashboard_menu', 'wpsc_dashboard', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icon-wpsc.png', 2); 
});

add_action( 'admin_bar_menu', 'admin_bar_item', 500 );
function admin_bar_item ( WP_Admin_Bar $admin_bar ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $admin_bar->add_menu( array(
        'id'    => 'wpsc-create-sc',
        'parent' => null,
        'group'  => null,
        'title' => '<img src="' . plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icon-wpsc.png' . '" style="vertical-align: middle;"> Create a Smart Contract',
        'href'  => admin_url('admin.php?page=wpsc_dashboard_menu'),
        'meta' => [
            'title' => 'Create a Smart Contract',
        ]
    ) );
}

function wpsc_dashboard() {
    
    $m = new Mustache_Engine;

    $atts["action"] = admin_url("admin.php?page=wpsc_dashboard_menu");

    $error_loading_json = false;

    $step = 1;
    if (WPSC_helpers::valArrElement($_GET, 'step')) {
        $step = (int) $_GET["step"];
    }
    if ($step===2) {
        if (WPSC_helpers::valArrElement($_GET, 'type')) {
            switch($_GET["type"]) {
            case "coin":
                $atts["is_coin"] = true;
                $atts["step2"] = true;
                break;
            case "stake":
                $atts["is_stake"] = true;
                $atts["step2"] = true;
                break;
            case "icos":
                $atts["is_ico"] = true;
                $atts["flavor"] = "Mango: A Safe Crowdfunding";
                $atts["flavor-slug"] = "mango";
                $atts["step3"] = true;
                break;
            case "nft":
                $atts["is_nft"] = true;
                $atts["step2"] = true;
                break;
            default:
                $atts["step1"] = true;
                break;
            }
            if (WPSC_helpers::valArrElement($atts, 'step3') and $atts["step3"]) {
                $temp = WPSC_helpers::getNetworkInfoJSON($atts["flavor-slug"]);
                if (is_array($temp)) {
                    $atts = array_merge($atts, $temp);
                } else {
                    $error_loading_json = true;
                }
            }
        } else {
            $atts["step1"] = true;
        }
    } elseif ($step===3) {
        if (WPSC_helpers::valArrElement($_GET, 'flavor')) {
            switch($_GET["flavor"]) {
            case "vanilla":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Vanilla: Gas Saving Token";
                $atts["is_coin"] = true;
                $atts["step3"] = true;
                break;
            case "pistachio":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Pistachio: Improved Security Token";
                $atts["is_coin"] = true;
                $atts["step3"] = true;
                break;
            case "chocolate":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Chocolate: Advanced Token";
                $atts["is_coin"] = true;
                $atts["step3"] = true;
                break;
            case "ube":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Ube: ERC-20 / BEP-20 Stakes";
                $atts["is_stake"] = true;
                $atts["step3"] = true;
                break;
            case "almond":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Almond: ERC-20 / BEP-20 Advanced Stakes";
                $atts["is_stake"] = true;
                $atts["step3"] = true;
                break;
            case "mango":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Mango: Safe Crowdfunding";
                $atts["is_ico"] = true;
                $atts["step3"] = true;
                break;
            case "raspberry":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Raspberry: Pausable ICO";
                $atts["is_ico"] = true;
                $atts["step3"] = true;
                break;
            case "bluemoon":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Bluemoon: Advanced ICO";
                $atts["is_ico"] = true;
                $atts["step3"] = true;
                break;
            case "mochi":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Mochi: Standard NFT Collection";
                $atts["is_nft"] = true;
                $atts["step3"] = true;
                break;
            case "matcha":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Matcha: NFT Marketplace";
                $atts["is_nft"] = true;
                $atts["step3"] = true;
                break;
            case "suika":
                $atts["flavor-slug"] = $_GET["flavor"];
                $atts["flavor"] = "Suika: NFT Token Marketplace with Royalties";
                $atts["is_nft"] = true;
                $atts["step3"] = true;
                break;
            }
            if (WPSC_helpers::valArrElement($atts, 'step3') and $atts["step3"]) {
                $temp = WPSC_helpers::getNetworkInfoJSON($_GET["flavor"]);
                if (is_array($temp)) {
                    $atts = array_merge($atts, $temp);
                } else {
                    $error_loading_json = true;
                }
            }
        }
    } else {
        $atts["step1"] = true;
    }
    
    $atts["logo"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/wpsmartcontracts.png';

    $atts["coins-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/coins.png';
    $atts["icos-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/icos.png';
    $atts["stakings-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/stakings.png';
    $atts["nfts-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/nfts.png';
    
    $atts["vanilla-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/vanilla-flavor.png';
    $atts["pistachio-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/pistachio-flavor.png';
    $atts["chocolate-img"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/chocolate-flavor.png';
    $atts["mochi-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/mochi-flavor.png";
    $atts["matcha-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/matcha-flavor.png";
    $atts["ube-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/ube-flavor.png";
    $atts["almond-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/almond-flavor.png";
    $atts["bluemoon-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/bluemoon-flavor.png";
    $atts["suika-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/suika-flavor.png";
    $atts["raspberry-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/raspberry-flavor.png";
    $atts["mango-img"] = plugin_dir_url( dirname( __FILE__)) . "assets/img/mango-flavor.png";
    
    $atts["ethereum-network"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/ethereum-network.png';
    $atts["optimistic-ethereum-network"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/optimistic-ethereum-network.png';
    $atts["arbitrum-network"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/arbitrum-network.png';
    $atts["rsk-network"]=plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/rsk-network.png';

    $atts["erc-20-gas-saving"] =  __('Gas Saving Token', 'wp-smart-contracts');
    $atts["erc-20-gas-saving-desc"] = __('A Standard ERC-20 Token, focused on Gas Saving transactions', 'wp-smart-contracts');
    $atts['erc-20-imp-sec'] = __('Improved Security Token', 'wp-smart-contracts');
    $atts["erc-20-imp-sec-desc"] = __('A Standard Ethereum Token, focused on security', 'wp-smart-contracts');
    $atts['erc-20-advanced'] = __('Advanced Token', 'wp-smart-contracts');
    $atts['erc-20-advanced-tooltip'] = __('An Standard ERC-20 Token, secure with advanced features.', 'wp-smart-contracts');
    $atts['crowdfunding-donation'] =  __('Safe Crowdfunding', 'wp-smart-contracts');
    $atts['crowdfunding-donation-desc'] =  __('A simple crowdfunding campaign that can receive contributions in Ether or the native coin of the blockchain.', 'wp-smart-contracts');
    $atts['ico-donation'] =  __('Pausable Initial Coin Offering', 'wp-smart-contracts');
    $atts['ico-donation-desc'] =  __('A simple Initial Coin Offering that can receive contributions in the native coin of the selected blockchain.', 'wp-smart-contracts');
    $atts['crowdsale-advanced'] =  __('Advanced Initial Coin Offering', 'wp-smart-contracts');
    $atts['crowdsale-advanced-desc'] =  __('An advanced crowdsale to create Initial Coin Offerings with multiple features.', 'wp-smart-contracts');
    $atts['mochi-desc'] = __("Standard NFT Collection", "wp-smart-contracts");
    $atts['mochi-desc-2'] = __("A simple ERC-721 Standard Token. You can create and transfer collectibles.", "wp-smart-contracts");
    $atts['matcha-desc'] = __("NFT Marketplace", "wp-smart-contracts");
    $atts['matcha-desc-2'] = __("Fully featured NFT Marketplace", "wp-smart-contracts");
    $atts['suika-desc'] = __("NFT Token Marketplace with Royalties", "wp-smart-contracts");
    $atts['suika-desc-2'] = __("Fully featured NFT ERC-20 / BEP20 Token Marketplace with auction, selling and royalties.", "wp-smart-contracts");
    $atts['text'] = __('To deploy your Smart Contracts you need to be connected to a Network. Please install and connect to Metamask.', 'wp-smart-contracts');
    $atts['fox'] = plugins_url( "assets/img/metamask-fox.svg", dirname(__FILE__) );
    $atts['connect-to-metamask'] = __('Connect to Metamask', 'wp-smart-contracts');

    if ($error_loading_json) {
        $atts["json-error"]=true;
        $atts["new-coin"] = admin_url("post-new.php?post_type=coin");
        $atts["new-staking"] = admin_url("post-new.php?post_type=staking");
        $atts["new-crowdfunding"] = admin_url("post-new.php?post_type=crowdfunding");
        $atts["new-ico"] = admin_url("post-new.php?post_type=ico");
        $atts["new-nft-collection"] = admin_url("post-new.php?post_type=nft-collection");
    }
    echo $m->render(WPSC_Mustache::getTemplate('wpsc-dashboard'), $atts);

}
