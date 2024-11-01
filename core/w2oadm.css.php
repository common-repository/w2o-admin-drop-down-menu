<?php
function make_link_relative( $link ) {
	return preg_replace('|https?://[^/]+(/.*)|i', '$1', $link );
}

function wp_w2oadm_true_if_set( $param ) {
	return ( ( isset( $_GET[ $param ] ) && $_GET[ $param ] == 1 ) ? true : false );
}

$plugin      		= isset( $_GET['p'] ) ? make_link_relative( $_GET['p'] ) : '';
$wpicons     		= wp_w2oadm_true_if_set( 'w' );
$wpiconsonly     	= wp_w2oadm_true_if_set( 'wo' );
$hidebubble  		= wp_w2oadm_true_if_set( 'h' );
$display_fav 		= wp_w2oadm_true_if_set( 'f' );
$dir         		= ( isset( $_GET['d'] ) && $_GET['d'] == 'right' ) ? 'right' : 'left' ; // text direction
$opdir       		= ( isset( $_GET['r'] ) && $_GET['r'] == 'right' ) ? 'left' : 'right' ; // OPposite DIRection

header("Content-type:text/css; charset:UTF-8");

?>
/* W2OADM Style */
/* Restyle or hide original items */
#adminmenu 					{display:none !important;}
#wpbody, div.folded #wpbody {margin-<?php echo $dir; ?>:0px}

#wpbody-content .wrap {margin-<?php echo $dir; ?>:15px}

#media-upload-header #sidemenu li {
	display:auto;
}

/* hidden in case we have no JS to move it */
/* disabled to fix the issue of Screen Options and Help tab not working properly */
/*
#screen-meta {
	display:none !important;
}
*/

#update-nag, .update-nag {margin-<?php echo $dir; ?>:15px}

/* added for WP 3.2 */
#adminmenuback, #adminmenuwrap, #adminmenu, .auto-fold #adminmenu, .auto-fold #adminmenu li.menu-top, .auto-fold #adminmenuback, .auto-fold #adminmenuwrap,  
.folded #adminmenu .wp-submenu.sub-open, .folded #adminmenu .wp-submenu-wrap,
.folded #adminmenuback, .folded #adminmenuwrap, .folded #adminmenu, .folded #adminmenu li.menu-top,
.js.folded #adminmenuback, .js.folded #adminmenuwrap, .js.folded #adminmenu, .js.folded #adminmenu li.menu-top {
    visibility:hidden !important;
}

.auto-fold #wpcontent, .auto-fold #wpfooter 

#wpcontent, .auto-fold #wpcontent, .folded #wpcontent, .js.folded #wpcontent {
    margin-left: 0px;
    padding-left:0px;
	margin-right:0px;
}
#footer, .auto-fold #wpfooter, .folded #footer, .js.folded #footer {
	margin-<?php echo $dir; ?>:15px;
    padding-left:0;
}
#wphead {
	background:#D1E5EE;
	margin-right:0px;
	margin-left:0px;
	padding-right:15px;
	padding-left:18px;
}

/* Fixes for the default issue of Wordpress that hides submenu (dropdown) behind when there are many admin menus on the top bar and when they overflows down */
#wpadminbar ul#wp-admin-bar-root-default > li, #wpadminbar ul#wp-admin-bar-top-secondary, #wpadminbar ul#wp-admin-bar-top-secondary > li {
	position:static;
}

#wpadminbar ul#wp-admin-bar-root-default li > .ab-sub-wrapper, #wpadminbar ul#wp-admin-bar-top-secondary li > .ab-sub-wrapper {
	min-width:auto !important;     
    *top:auto !important; /** for <= IE7 **/
    *left:auto !important; /** for <= IE7 **/
}
/* End of the fixes for the default issue of Wordpress that hides submenu (dropdown) behind */

/* Making circle for wp admin notification bubble with one default background color so that they are identified quickly and distinctly */
#wpadminbar li a .update-plugins, #wpadminbar li a .awaiting-mod {
	float:right;
    width: 14px; 
    height: 14px; 
    background: #0a9600; /* #afcd42; */
    -moz-border-radius: 50px; 
    -webkit-border-radius: 50px; 
    border-radius: 50px;
    top:2px;
    *top:1px; /** for <= IE7 **/    
    <?php if ($wpicons && $wpiconsonly) { echo 'margin-left:-7px; margin-bottom:6px;'; } else { echo 'margin-left:-4px;'; }?>
    *margin-left:-2px; /** for <= IE7 **/
    opacity:1 !important;
}
.ie8 #wpadminbar li a .update-plugins, .ie8 #wpadminbar li a .awaiting-mod {
	margin-left:0px;
}

#wpadminbar li a .update-plugins:hover, #wpadminbar li a .awaiting-mod:hover {
	background: #de5e60; 
}

#wpadminbar li a .awaiting-mod span, #wpadminbar li a .update-plugins span {
	font-size:11px;
    color:#fff;
    position: relative;
    margin-left: 0.37em;
    *margin-left: -3px; /** for <= IE7 **/
    top: -10px;
}
#wpadminbar li a .awaiting-mod span {
	margin-left: 0.35em;
    *margin-left: -3px; /** for <= IE7 **/
}

#wpadminbar li#wp-admin-bar-comments span.awaiting-mod {
	margin-left:-9px;
    *position:relative;
    *top: 8px; /** for <= IE7 **/
}
#wpadminbar li#wp-admin-bar-comments a.ab-item {
	*margin-left:-6px; /** for <= IE7 **/
}

/* End of Making circle for wp admin notifications */

/* for IE (<= IE7 only) */
.no-font-face #wpadminbar ul.ab-top-menu > li > a.ab-item { width: auto !important; overflow:visible !important; }

/* for IE7 only */
#wphead {
	#border-top-width: 31px;
}
#media-upload-header #sidemenu { display: block !important; }


<?php if (!$display_fav) { ?>
/* Hide favorite actions */
#favorite-actions {display:none !important;}
<?php } ?>

<?php if ($hidebubble) { ?>
/* Hide "0" bubbles */
span.count-0 {display:none !important;}
<?php } ?>

.customize-support .w2oadm_menu_sublevel a[href="themes.php?page=custom-header"], .customize-support .w2oadm_menu_sublevel a[href="themes.php?page=custom-background"] {
	display:none !important;
}

/* Some CSS codes for W2OADM classes, IDs  (initialize) */
.w2oadm_topmenu {
    display:block !important;
}
.w2oadm_topmenu > a.ab-item {
	overflow:hidden !important;
}
.w2oadm_wpbody {
 	padding-top:32px !important;
 }
 .w2oadm_wp-admin-bar-top-secondary, #wpadminbar .quicklinks .ab-top-secondary.w2oadm_wp-admin-bar-top-secondary > li {
 	float:left !important;
 }
.w2oadm_wp-admin-bar-my-account_ab-sub-wrapper {
	right:auto !important;
    
    *left:auto !important;
    *top:auto !important;
 }

.w2oadm_customtag_toplevel { 
    padding-left:6px !important;
    display:inline-block !important;
}

#wpadminbar div.w2oadm_menu_text_toplevel, #wpadminbar div.w2oadm_menu_imageonly_toplevel {
	position:relative;
}

img.w2oadm_menu_imageonly_toplevel {
	vertical-align:text-bottom;
    max-width:20px;
    max-height:20px;
}

/* Fixes for Wordfence Save Settings section getting hidden inside the top nav when menus doubles up */
.wf-options-controls {
    top:inherit !important;
    left: 0 !important;
}

/* W2OADM Style Ends */