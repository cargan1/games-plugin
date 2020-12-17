<?php
/**
 * Plugin Name: Games API
 * Description: API til import af games fra Casino Engine
 * Version: 1.0.0
 * Author: 3WM
 * Author URI: https://www.3wm.dk
 * Text Domain: gamesapi
 */

add_action( 'plugins_loaded', 'gamesapi_init', 0 );

function gamesapi_init() {
    
    $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/';
	load_plugin_textdomain( 'gamesapi', false, $plugin_rel_path );
    wp_enqueue_style( 'games_style', '/wp-content/plugins/games-api/style.css', false, "1.0.0" );
    
}

add_action( 'init', function() {
    remove_post_type_support( 'games', 'editor' );
}, 99);

add_action( 'admin_enqueue_scripts', 'add_stylesheet_to_admin' );

function add_stylesheet_to_admin() {
	$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/';
	wp_enqueue_style( 'games-admin-css', plugins_url('admin.css', __FILE__) );
}


function register_post_type_game() {

	/**
	 * Post Type: Games.
	 */

	$labels = array(
		"name" => __( "Games", "gamesapi" ),
		"singular_name" => __( "Game", "gamesapi" ),
		"menu_name" => __( "Games", "gamesapi" ),
		"all_items" => __( "All games", "gamesapi" ),
		"add_new" => __( "Add game", "gamesapi" ),
		"add_new_item" => __( "Add new game", "gamesapi" ),
		"edit_item" => __( "Edit game", "gamesapi" ),
		"new_item" => __( "New game", "gamesapi" ),
		"view_item" => __( "View game", "gamesapi" ),
		"view_items" => __( "Vis games", "gamesapi" ),
		"search_items" => __( "Search games", "gamesapi" ),
		"not_found" => __( "No games found", "gamesapi" ),
		"not_found_in_trash" => __( "No games in trash", "gamesapi" ),
		"parent_item_colon" => __( "Parent game", "gamesapi" ),
		"featured_image" => __( "Featured image for game", "gamesapi" ),
		"set_featured_image" => __( "Set featured image for game", "gamesapi" ),
		"remove_featured_image" => __( "Remove featured image for game", "gamesapi" ),
		"use_featured_image" => __( "Set featured image for game", "gamesapi" ),
		"archives" => __( "Game archives", "gamesapi" ),
		"parent_item_colon" => __( "Parent game", "gamesapi" ),
	);

	$args = array(
		"label" => __( "Games", "gamesapi" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"delete_with_user" => false,
		"show_in_rest" => true,
		"rest_base" => "games",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "game", "with_front" => true ),
		"query_var" => true,
		"supports" => array( "title", "thumbnail", "excerpt", "custom-fields" ),
	);

	register_post_type( "game", $args );

}

add_action( 'init', 'register_post_type_game' );



function register_game_taxonomies() {

	/**
	 * Taxonomy: Game category.
	 */

	$labels = array(
		"name" => __( "Game categories", "gamesapi" ),
		"singular_name" => __( "Game category", "gamesapi" ),
		"all_items" => __( "All categories", "gamesapi" ),
		"edit_item" => __( "Edit category", "gamesapi" ),
		"view_item" => __( "View category", "gamesapi" ),
		"update_item" => __( "Update category", "gamesapi" ),
		"add_new_item" => __( "Add new category", "gamesapi" ),
	);

	$args = array(
		"label" => __( "Categories", "gamesapi" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'gamecategory', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "gamecategory",
		"show_in_quick_edit" => false,
		);
	register_taxonomy( "gamecategory", array( "game" ), $args );

	/**
	 * Taxonomy: Modeller.
	 */
/*
	$labels = array(
		"name" => __( "Modeller", "gamesapi" ),
		"singular_name" => __( "Model", "gamesapi" ),
		"menu_name" => __( "Modeller", "gamesapi" ),
		"all_items" => __( "Alle modeller", "gamesapi" ),
		"edit_item" => __( "Redigér model", "gamesapi" ),
		"view_item" => __( "Vis model", "gamesapi" ),
		"update_item" => __( "Opdatér modelnavn", "gamesapi" ),
		"add_new_item" => __( "Tilføj model", "gamesapi" ),
	);

	$args = array(
		"label" => __( "Modeller", "gamesapi" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'bilmodel', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "bilmodel",
		"show_in_quick_edit" => false,
		);
    register_taxonomy( "bilmodel", array( "bil", "bil_kontant" ), $args );
    */

}
add_action( 'init', 'register_game_taxonomies' );





function isThisNew($time) {
	$stamp = strtotime($time);
	if ($stamp > (time() - (3600 * 24 * 7 * 2))) {
		return true;
	}
	else {
		return false;
	}
}

function checkIfGameExists($gameid) {
	$args = array(
	    'meta_key' => 'game_id',
	    'meta_value' => $gameid,
	    'post_type' => 'game',
	    'post_status' => 'any',
	    'posts_per_page' => -1
	);
    $posts = get_posts($args);
    
	if (count($posts) > 0) {
		return $posts[0]->ID;
	}
	else {
		return false;
	}
}

function createGame($data) {

	$post_arr = array(
                'post_title'   => $data['data']['presentation']['gameName']['*'] . ' - ' . $data['id'],
                'post_status'  => 'publish',
				'post_type'    => 'game',
				'post_name'	   => $data['data']['slug'],
                'meta_input'   => array(
										'game_name' => $data['data']['presentation']['gameName']['*'] ?? null,
										'data_type' => $data['type'] ?? null,
										'game_id' => $data['id'] ?? null,
										'game_seq' => $data['seq'] ?? null,
                                        'game_categories' => implode(',', $data['data']['categories']) ?? null,
                                        'game_vendor' => $data['data']['vendorDisplayName'] ?? null,
										'game_provider' => $data['data']['contentProvider'] ?? null,
										'vendor_id' => $data['data']['vendorID'] ?? null,
										'bundle_id' => $data['data']['gameBundleID'] ?? null,
										'game_tags' => implode(',', $data['data']['tags']) ?? null,
                                        'game_logo' => $data['data']['presentation']['logo']['*'] ?? null,
										'game_thumbnail' => $data['data']['presentation']['thumbnail']['*'] ?? null,
										'game_payout' => $data['data']['theoreticalPayOut'] ?? null,
										'game_tpf' => $data['data']['thirdPartyFee'] ?? null,
										'game_fpp' => $data['data']['fpp'] ?? null,
										'country_restrictions' => implode(',', $data['data']['restrictedTerritories']) ?? null,
										'game_languages' => implode(',', $data['data']['languages']) ?? null,
										'game_currencies' => implode(',', $data['data']['currencies']) ?? null,
										'new_game' => isThisNew($data['data']['creation']['time']) ?? null,
										'new_expiration' => $data['data']['creation']['newGameExpiryTime'] ?? null,
										'property_width' => $data['data']['property']['width'] ?? null,
										'property_height' => $data['data']['property']['height'] ?? null,
										'property_license' => $data['data']['property']['license'] ?? null,
										'property_coin' => $data['data']['property']['defaultCoin'] ?? null,
										'property_terminal' => implode(',', $data['data']['property']['terminal']) ?? null,
										'property_jurisdictions' => implode(',', $data['data']['property']['jurisdictions']) ?? null,
										'freespin_support' => $data['data']['property']['freeSpin']['support'] ?? null,
										'freespin_betvalues' => implode(',', $data['data']['property']['freeSpin']['betValues']['selections']) ?? null,
										'freespin_feature_support' => $data['data']['property']['freeSpin']['supportFeatureBonus'] ?? null,
										'freespin_feature_support' => $data['data']['property']['hitFrequency']['min'] ?? null,
										'hit_frequency_min' => $data['data']['property']['hitFrequency']['min'] ?? null,
										'hit_frequency_max' => $data['data']['property']['hitFrequency']['min'] ?? null,
										'html5_launch' => $data['data']['property']['launchGameInHtml5'] ?? null,
										'age_limit' => $data['data']['property']['ageLimit'] ?? null,
										'popularity_coefficient' => $data['data']['popularity']['coefficient'] ?? null,
										'popularity_ranking' => $data['data']['popularity']['ranking'] ?? null,
										'playmode_fun' => $data['data']['playMode']['fun'] ?? null,
										'playmode_anonymity' => $data['data']['playMode']['anonymity'] ?? null,
										'playmode_realmoney' => $data['data']['playMode']['realMoney'] ?? null,
										'jackpot_type' => $data['data']['jackpot']['type'] ?? null,
										'jackpot_contribution' => $data['data']['jackpot']['contribution'] ?? null,
										'contribution_enabled' => $data['data']['jackpot']['contributionEnable'] ?? null,
										'bonus_contribution' => $data['data']['bonus']['contribution'] ?? null,
										'bonus_overridable' => $data['data']['bonus']['overridable'] ?? null,
										'bonus_excluded' => $data['data']['bonus']['excluded'] ?? null,
										'game_shortname' => $data['data']['presentation']['shortName']['*'] ?? null,
										'game_presentation' => $data['data']['presentation']['description'] ?? null,
										'top_prize' => $data['data']['topPrize'] ?? null,
										'vendor_limits_max_bet' => $data['data']['vendorLimits']['defaultMaxBet']['EUR'] ?? null,
										'vendor_limits_max_multiplier' => $data['data']['vendorLimits']['defaultMaxMultiplier'] ?? null,
										'vendor_limits_max_win' => $data['data']['vendorLimits']['defaultMaxWin']['EUR'] ?? null,
										'additional_highstake' => $data['data']['additional']['highStake']['value'] ?? null,
										'additional_fullscreen' => $data['data']['additional']['fullScreen']['value'] ?? null			
										
                )
            );


                        $newgameid = wp_insert_post($post_arr);

                        wp_set_post_terms($newgameid, $data['data']['report']['category'], 'gamecategory');
                        
						//wp_set_post_terms($newcarid, bodyTypeToCat($data['BodyTypes'], $data['Propellant'], $data['SecondaryPropellant']), 'bilkategori');

}


function updateGame($id, $data) {
	$post_arr = array(
				'ID'					 => $id,
                'post_title'   => $data['data']['presentation']['gameName']['*'] . ' - ' . $data['id'],
                'post_status'  => 'publish',
				'post_type'    => 'game',
				'post_name'	   => $data['data']['slug'],
                'meta_input'   => array(
                    'game_name' => $data['data']['presentation']['gameName']['*'] ?? null,
                    'data_type' => $data['type'] ?? null,
                    'game_id' => $data['id'] ?? null,
                    'game_seq' => $data['seq'] ?? null,
                    'game_categories' => implode(',', $data['data']['categories']) ?? null,
                    'game_vendor' => $data['data']['vendorDisplayName'] ?? null,
                    'game_provider' => $data['data']['contentProvider'] ?? null,
                    'vendor_id' => $data['data']['vendorID'] ?? null,
                    'bundle_id' => $data['data']['gameBundleID'] ?? null,
                    'game_tags' => implode(',', $data['data']['tags']) ?? null,
                    'game_logo' => $data['data']['presentation']['logo']['*'] ?? null,
                    'game_thumbnail' => $data['data']['presentation']['thumbnail']['*'] ?? null,
                    'game_payout' => $data['data']['theoreticalPayOut'] ?? null,
                    'game_tpf' => $data['data']['thirdPartyFee'] ?? null,
                    'game_fpp' => $data['data']['fpp'] ?? null,
                    'country_restrictions' => implode(',', $data['data']['restrictedTerritories']) ?? null,
                    'game_languages' => implode(',', $data['data']['languages']) ?? null,
                    'game_currencies' => implode(',', $data['data']['currencies']) ?? null,
                    'new_game' => isThisNew($data['data']['creation']['time']) ?? null,
                    'new_expiration' => $data['data']['creation']['newGameExpiryTime'] ?? null,
                    'property_width' => $data['data']['property']['width'] ?? null,
                    'property_height' => $data['data']['property']['height'] ?? null,
                    'property_license' => $data['data']['property']['license'] ?? null,
                    'property_coin' => $data['data']['property']['defaultCoin'] ?? null,
                    'property_terminal' => implode(',', $data['data']['property']['terminal']) ?? null,
                    'property_jurisdictions' => implode(',', $data['data']['property']['jurisdictions']) ?? null,
                    'freespin_support' => $data['data']['property']['freeSpin']['support'] ?? null,
                    'freespin_betvalues' => implode(',', $data['data']['property']['freeSpin']['betValues']['selections']) ?? null,
                    'freespin_feature_support' => $data['data']['property']['freeSpin']['supportFeatureBonus'] ?? null,
                    'freespin_feature_support' => $data['data']['property']['hitFrequency']['min'] ?? null,
                    'hit_frequency_min' => $data['data']['property']['hitFrequency']['min'] ?? null,
                    'hit_frequency_max' => $data['data']['property']['hitFrequency']['min'] ?? null,
                    'html5_launch' => $data['data']['property']['launchGameInHtml5'] ?? null,
                    'age_limit' => $data['data']['property']['ageLimit'] ?? null,
                    'popularity_coefficient' => $data['data']['popularity']['coefficient'] ?? null,
                    'popularity_ranking' => $data['data']['popularity']['ranking'] ?? null,
                    'playmode_fun' => $data['data']['playMode']['fun'] ?? null,
                    'playmode_anonymity' => $data['data']['playMode']['anonymity'] ?? null,
                    'playmode_realmoney' => $data['data']['playMode']['realMoney'] ?? null,
                    'jackpot_type' => $data['data']['jackpot']['type'] ?? null,
                    'jackpot_contribution' => $data['data']['jackpot']['contribution'] ?? null,
                    'contribution_enabled' => $data['data']['jackpot']['contributionEnable'] ?? null,
                    'bonus_contribution' => $data['data']['bonus']['contribution'] ?? null,
                    'bonus_overridable' => $data['data']['bonus']['overridable'] ?? null,
                    'bonus_excluded' => $data['data']['bonus']['excluded'] ?? null,
                    'game_shortname' => $data['data']['presentation']['shortName']['*'] ?? null,
                    'game_presentation' => $data['data']['presentation']['description'] ?? null,
                    'top_prize' => $data['data']['topPrize'] ?? null,
                    'vendor_limits_max_bet' => $data['data']['vendorLimits']['defaultMaxBet']['EUR'] ?? null,
                    'vendor_limits_max_multiplier' => $data['data']['vendorLimits']['defaultMaxMultiplier'] ?? null,
                    'vendor_limits_max_win' => $data['data']['vendorLimits']['defaultMaxWin']['EUR'] ?? null,
                    'additional_highstake' => $data['data']['additional']['highStake']['value'] ?? null,
                    'additional_fullscreen' => $data['data']['additional']['fullScreen']['value'] ?? null
                )
            );
						wp_update_post($post_arr);
                        wp_set_post_terms($id, $data['data']['report']['category'], 'gamecategory');
					  
					}


function importGames() {
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://casino2-stage.everymatrix.com/jsonfeeds/mix/CEFeedSandbox?types=Game",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
    ));
    
    $response = curl_exec($curl);
    
	curl_close($curl);

	$response = str_replace(array("\n\r", "\n", "\r"), "", $response);
	$response = "[" . str_replace("}{", "},{", $response) . "]";
	
    $array = json_decode( $response, true );

	/* Debugging code results
    $fp = fopen('debugdecode.txt', 'w');  
    fwrite($fp, gettype($array));  
    fclose($fp); */

    if(is_array($array)){

	foreach($array as $game) {
		
        $result = checkIfGameExists($game['id']);
        
            if ($result == false) {
                createGame($game);
            }
            else {
                updateGame($result, $game);
            }
    }
    }
}


add_action( 'do_synchronize_games_3wm', 'importGames' );

/*
**
** Cron Job function
**
*/


function active_scheduled_synchronization_3wm() {
    if (!wp_next_scheduled('do_synchronize_games_3wm')) {
        wp_schedule_event(time(), 'daily', 'do_synchronize_games_3wm');
    }
}
register_activation_hook( __FILE__,'active_scheduled_synchronization_3wm');

function deactivate_scheduled_synchronization_3wm() {
    wp_clear_scheduled_hook('do_synchronize_games_3wm');
}
register_deactivation_hook( __FILE__,'deactivate_scheduled_games_3wm');

