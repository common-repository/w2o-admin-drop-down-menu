<?php
/*
Plugin Name: W2O Admin Dropdown Menu
Plugin URI: https://wordpress.org/plugins/w2o-admin-drop-down-menu/
Description: Neat, clean, responsive and Wordpress environment friendly horizontal dropdown menu for Admin that eliminates the left menu and saves screen space <em><strong>(For WordPress 4.0+)</strong></em>. If you like this plugin then why don't you drop an email at <strong>shishir.adhikari@gmail.com</strong> saying "I loved it...". I would really appreciate that.
Version: 3.0
Author: Shishir Raj Adhikari
Author URI: https://profiles.wordpress.org/shishiradhikari/
License: GPLv2 or later
*/

/*  Copyright 2016  W2o Admin Dropdown Menu (Author: Shishir Raj Adhikari)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

#Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'W2OADM_VER', '3.0' );
 
/***** Hooks of the plugins when visiting an admin page for W2O Admin Dropdown Menu (W2OADM). ****/
if ( is_admin() ){
	global $wp_w2oadm;

	require_once(dirname(__FILE__).'/core/core.php');
	add_action('admin_init', 'wp_w2oadm_init', -1000);	// Init	
	add_action('admin_head', 'wp_w2oadm_head', 999); // Insert CSS, JS etc to preload inside admin section's <head>
    /*
     * Making the plugin compatible with Admin Menu Editor plugin, i.e. https://wordpress.org/plugins/admin-menu-editor/
     */
    if(isset($wp_menu_editor)) {
        if(is_object($wp_menu_editor)) {
            add_filter('admin_bar_menu', array($wp_menu_editor, 'replace_wp_menu'));
        }
    }
	add_filter('admin_bar_menu', 'wp_w2oadm', 2000); // Generate new W2O Admin Dropdown Menu
	add_action( 'admin_footer' , 'wp_w2oadm_footer', 1979); // Insert JavaScript (JS) inside admin footer section
}
// Hiding admin bar on the site page when logged in. Un-comment (remove //) if needed
//add_filter('show_admin_bar', '__return_false');

// Making sure it is WP 4.0+ only
function wp_w2oadm_versioncheck(){
	global $wp_version;
	if ( version_compare($wp_version, '4.0', '<') ) {
		deactivate_plugins( basename(__FILE__) );
		wp_die("Sorry, this plugin requires WordPress 4.0 at least");
	}
}
register_activation_hook(__FILE__, 'wp_w2oadm_versioncheck');