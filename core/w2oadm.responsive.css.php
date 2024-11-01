<?php
function make_link_relative( $link ) {
	return preg_replace('|https?://[^/]+(/.*)|i', '$1', $link );
}

function wp_w2oadm_true_if_set( $param ) {
	return ( ( isset( $_GET[ $param ] ) && $_GET[ $param ] == 1 ) ? true : false );
}

$plugin      = isset( $_GET['p'] ) ? make_link_relative( $_GET['p'] ) : '';
$wpicons     = wp_w2oadm_true_if_set( 'w' );
$wpiconsonly = wp_w2oadm_true_if_set( 'wo' );
$hidebubble  = wp_w2oadm_true_if_set( 'h' );
$display_fav = wp_w2oadm_true_if_set( 'f' );
$dir         = ( isset( $_GET['d'] ) && $_GET['d'] == 'right' ) ? 'right' : 'left' ; // text direction
$opdir       = ( isset( $_GET['r'] ) && $_GET['r'] == 'right' ) ? 'left' : 'right' ; // OPposite DIRection

header('Content-type:text/css');
?>
/* W2OADM Style for Mobile screens - Using WP default media screen max-width for admin bar menu (top menu) */

@media screen and ( max-width: 782px ) {
	
    /* Hide Admin bar menus (excepts My Account menu) to retain back the orignal state of WP admin menus for mobile screens */
    .w2oadm_topmenu {
    	display:none !important;
    }

    /* Reset back the original items for mobile screens */
    #adminmenu {
        display:block !important;
    }
    
    /* added for WP 3.2 */
    #adminmenuback, #adminmenuwrap, #adminmenu, .auto-fold #adminmenu, .auto-fold #adminmenu li.menu-top, .auto-fold #adminmenuback, .auto-fold #adminmenuwrap, 
    .folded #adminmenu .wp-submenu.sub-open, .folded #adminmenu .wp-submenu-wrap,
    .folded #adminmenuback, .folded #adminmenuwrap, .folded #adminmenu, .folded #adminmenu li.menu-top,
    .js.folded #adminmenuback, .js.folded #adminmenuwrap, .js.folded #adminmenu, .js.folded #adminmenu li.menu-top {
        visibility:visible !important;
    }
    
    #wpadminbar ul#wp-admin-bar-root-default > li {
        position:relative;
        display:none !important;
    }
    
    #wpadminbar ul#wp-admin-bar-root-default li > .ab-sub-wrapper {
        min-width:100% !important;
    }
    
    #wpadminbar ul#wp-admin-bar-root-default > li#wp-admin-bar-menu-toggle {
        display:block !important;
    }
    
    #wphead {
        background:#D1E5EE;
        margin-right:auto;
        margin-left:auto;
        padding-right:auto;
        padding-left:auto;
    }
    
    /* Exceptional case, for adjusting the "My Account" section (especially its text "Howdy..." to align properly as some plugins distort this section). This css code can be removed if it does not cause any problem on your wp admin */
    #wp-admin-bar-top-secondary li#wp-admin-bar-my-account.with-avatar > .ab-item::before {
        content:no-open-quote !important;
    }
}

@media screen and ( max-width: 480px ) {
	/* Adjusting Wordpress "update" section (when it appears) for small mobile screen (i.e. for max-width: 480px) */
    #update-nag, .update-nag {
    	margin-top:70px;
    }
}
/* W2OADM Style for Mobile screens Ends */