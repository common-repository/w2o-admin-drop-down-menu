<?php
#Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// The main function that hacks the original menu and display ours instead. This function is called in "w2o-admin-drop-down-menu.php" and triggers by the admin_bar_menu hook.
function wp_w2oadm () {	
	global $wp_admin_bar;
	global $menu, $submenu, $self, $parent_file, $submenu_file, $plugin_page, $pagenow, $wp_w2oadm;

    // setting the parent menu link to be the first child menu link
    $submenu_as_parent = true;

	// Plugins: hack $menu & $submenu
	$menu = apply_filters( 'pre_w2oadm_menu', $menu );
	$submenu = apply_filters( 'pre_w2oadm_submenu', $submenu ); 
	
	// Plugins: hack wp_admin_bar (top bar menu)
	$w2o_admin_bar = apply_filters( 'pre_w2oadm_admin_bar_menu', $wp_admin_bar ); 
	$w2o_admin_bar_nodes = $w2o_admin_bar->get_nodes();
	
	$w2oadm_customtag_toplevel = '<w2o class="w2oadm_customtag_toplevel">[~title~]</w2o>';
	// adding first Menu as Blog Title (Site link)
	wp_w2oadm_blogtitle();
	
	$first = true;
	// 0 = name, 1 = capability, 2 = file, 3 = class, 4 = id, 5 = icon src
	foreach ( $_menu = $menu as $key => $item ) {
		// Top level menu
		if (strpos($item[4],'wp-menu-separator') !== false)
			continue;
		
		$admin_is_parent = false;
		$class = array();
		if ( $first ) {
			$class[] = 'wp-first-item';
			$first = false;
		}
		if ( !empty($submenu[$item[2]]) )
			$class[] = 'wp-has-submenu';

		if ( ( $parent_file && $item[2] == $parent_file ) || strcmp($self, $item[2]) == 0 ) {
			if ( !empty($submenu[$item[2]]) )
				$class[] = 'wp-has-current-submenu current wp-menu-open';
			else
				$class[] = 'current';
		}

		if ( isset($item[4]) && ! empty($item[4]) )
			$class[] = $item[4];

		$class = $class ? ' class="' . join( ' ', $class ) . '"' : '';
		$org_id = trim($item[5]);
		$id = isset($item[5]) && ! empty($item[5]) ? 'w2oadm_' . $item[5] : '';
		$anchor = $item[0];
		
		if ( isset( $submenu_as_parent ) && !empty( $submenu[$item[2]] ) ) {
			$submenu[$item[2]] = array_values($submenu[$item[2]]);  // Re-index.
			$menu_hook = get_plugin_page_hook($submenu[$item[2]][0][2], $item[2]);
			if ( ( ('index.php' != $submenu[$item[2]][0][2]) && file_exists(WP_PLUGIN_DIR . "/{$submenu[$item[2]][0][2]}") ) || !empty($menu_hook)) {

				$admin_is_parent = true;
				$href = "admin.php?page={$submenu[$item[2]][0][2]}";
			} else {
				$href = $submenu[$item[2]][0][2];
			}
		} else if ( current_user_can($item[1]) ) {
			$menu_hook = get_plugin_page_hook($item[2], 'admin.php');
			if ( ('index.php' != $item[2]) && file_exists(WP_PLUGIN_DIR . "/{$item[2]}") || !empty($menu_hook) ) {
				$admin_is_parent = true;
				$href = "admin.php?page={$item[2]}";
			} else {
				$href = $item[2];
			}
		}
		
		$img = '';
		if ($wp_w2oadm['wpicons']) {		
			
			if ( isset($item[6]) && ! empty($item[6]) ) {
				preg_match('/^dashicons/', $item[6], $matches);
				if ( 'none' === $item[6] || 'div' === $item[6] )
					$img = '<div class="wp-menu-image w2oadm_menu_text_toplevel">'.$w2oadm_customtag_toplevel.'</div>';
				elseif (!empty($matches))
					$img = '<div class="wp-menu-image w2oadm_menu_text_toplevel dashicons-before '.$item[6].'">'.$w2oadm_customtag_toplevel.'</div>';
				else {
					preg_match_all('/^data:image\/svg\+xml;base64,(.*)/', $item[6], $matches_svg);								
					if ( !empty($matches_svg) && ( isset($matches_svg[1][0]) && !empty($matches_svg[1][0]) ) ) {
						$svg_decoded = base64_decode ($matches_svg[1][0]);
						$svg_xml = preg_replace ('/fill="(.+?)"/', '', $svg_decoded);
						$svg_xml = preg_replace('/style="(.+?)"/', '', $svg_xml);							
						$svg_xml = preg_replace('/fill:.*?;/', '', $svg_xml);										
						
						$img = '<div class="wp-menu-image w2oadm_menu_image_toplevel"><div class="w2oadm_svg w2oadm_menu_imageonly_toplevel" style="float:left;">'.$svg_xml.'</div>'.$w2oadm_customtag_toplevel.'</div>';						
					}
					else { 
						// set default width and height of the parent menu icon that best fit in the horizontal menu bar
						$width ='20px';
						$height ='20px';
						if (function_exists('getimagesize')) {
							list($width, $height) = @getimagesize($item[6]);
							$width = ($width>0 && $width<=20) ? $width : '20';
							$width = $width.'px';

							$height = ($height>0 && $height<=20) ? $height : '20';
							$height = $height.'px';
						}
						$inline_style = ' style="width:'.$width.'; height:'.$height.';"';						
						$img = '<div class="wp-menu-image w2oadm_menu_image_toplevel"><img class="w2oadm_menu_imageonly_toplevel" src="' . $item[6] . '" border="0"'.$inline_style.' />'.$w2oadm_customtag_toplevel.'</div>';
					}
				}
			}
		} else {
			$img = '[~title~]';
		}		
			
		if ($wp_w2oadm['toplinks']) {
			$href = $href;
		} else {
			$href =  ( !empty($submenu[$item[2]]) )? '' : $href ;
		}		
		
		$topmenuTitle = str_replace('[~title~]', strip_tags($anchor,'<span><w2o>'), $img);
		// Top/Parent level menu here
		$wp_admin_bar->add_menu(array('id' => $id, 'title' => __($topmenuTitle), 'href' => $href, 'meta'=>array('title'=> strip_tags($anchor), 'class' => 'w2oadm_topmenu') ));
		
		// Sub level menus
		if ( !empty($submenu[$item[2]]) ) {
			if( !isset( $ulclass ) )
				$ulclass = '';
			
			$first = true;
			$counter = 1;
			foreach ( $submenu[$item[2]] as $sub_key => $sub_item ) {
				if ( !current_user_can($sub_item[1]) )
					continue;
				
				$class = array();
				if ( $first ) {
					$class[] = 'wp-first-item';
					$first = false;
				}
				if ( isset($submenu_file) ) {
					if ( $submenu_file == $sub_item[2] )
						$class[] = 'current';
				// If plugin_page is set the parent must either match the current page or not physically exist.
				// This allows plugin pages with the same hook to exist under different parents.
				} else if ( (isset($plugin_page) && $plugin_page == $sub_item[2] && (!file_exists($item[2]) || ($item[2] == $self))) || (!isset($plugin_page) && $self == $sub_item[2]) ) {
					$class[] = 'current';
				}

				$subclass = $class ? ' class="' . join( ' ', $class ) . '"' : '';

				$menu_hook = get_plugin_page_hook($sub_item[2], $item[2]);
				
				if ( ( ('index.php' != $sub_item[2]) && file_exists(WP_PLUGIN_DIR . "/{$sub_item[2]}") ) || ! empty($menu_hook) ) {
					// If admin.php is the current page or if the parent exists as a file in the plugins or admin dir
					$parent_exists = (!$admin_is_parent && file_exists(WP_PLUGIN_DIR . "/{$item[2]}") && !is_dir(WP_PLUGIN_DIR . "/{$item[2]}") ) || file_exists($item[2]);
					if ( $parent_exists )
						$suburl = "{$item[2]}?page={$sub_item[2]}";
					elseif ( 'admin.php' == $pagenow || !$parent_exists )
						$suburl = "admin.php?page={$sub_item[2]}";
					else
						$suburl = "{$item[2]}?page={$sub_item[2]}";
						
				} else {
					$suburl = $sub_item[2];
				}
	
				$subid = 'w2oadm_submenu'.$counter.'_of_'.$id;
				$subanchor = strip_tags($sub_item[0]);				
				
				$subclass_main ='w2oadm_sub_'.wp_w2oadm_sanitize_id($sub_item[2])." ";
				
				// Sub/Child menu of respective parent menu here					
				$wp_admin_bar->add_menu(array('parent' => $id, 'title' => __($subanchor), 'id' => $subid, 'href' => $suburl, 'meta'=>array('title'=> strip_tags($subanchor), 'class' => $subclass_main.'w2oadm_menu_sublevel') ));	
				
				$counter++;
			}			
			
		} // End of Sub level menus if condition
		
	}

	// Clean the default AdminBar
    foreach( $w2o_admin_bar_nodes as $node )
    {
		/** 
			* you can use $retain_nodes array to retain specific menu from being removed OR $remove_nodes array to remove specific top menus 
			* Using $retain_nodes, you should use if condition as "if(!in_array($node->id, $retain_nodes))"
			* Using $remove_nodes, you should use if condition as "if(in_array($node->id, $remove_nodes))"
			* Examples of '$remove_nodes':
			* $remove_nodes = array('wp-logo', 'about', 'wporg', 'documentation', 'support-forums', 'feedback', 'site-name', 'view-site', 'updates', 'comments', 'new-content', 'new-post', 'new-media', 'new-page', 'new-user', 'wp-logo-external');		
			* $remove_nodes = array('wp-logo', 'about', 'wporg', 'documentation', 'support-forums', 'feedback', 'updates', 'comments', 'new-content', 'new-post', 'new-media', 'new-page', 'new-user', 'wp-logo-external');
			*
			* NOTE: To handle exception case, 'top-secondary' used in $retain_nodes is for the User Actions right side menu (or say 'Howdy..' dropdown menu). 
			* To remove all top menus you can use the if condition as "if( !$node->parent || 'top-secondary' == $node->parent )"
		**/
		if ( isset($node->parent) && $node->parent ) {
			// do nothing
		} else {
			$w2oadm_customtag_toplevel_org = '<w2o class="w2oadm_customtag_toplevel_org">[~title~]</w2o>';	
			$node->title = str_replace('[~title~]', strip_tags($node->title, '<span><w2o>'), $w2oadm_customtag_toplevel_org);	
			$w2o_admin_bar->add_node($node);
		}
		
		$retain_nodes = array('user-actions', 'user-info', 'edit-profile', 'logout', 'menu-toggle', 'my-account', 'top-secondary');	
		if(!in_array($node->id, $retain_nodes))
        {	
			$w2o_admin_bar->remove_menu( $node->id );
		}           
    }
	
}

function wp_w2oadm_blogtitle() {
	global $wp_admin_bar, $wp_w2oadm;
	
	$blogname = get_bloginfo('name', 'display');
	if ( '' == $blogname )
		$blogname = '&nbsp;';
	$title_class = '';
	if ( function_exists('mb_strlen') ) {
		if ( mb_strlen($blogname, 'UTF-8') > 30 )
			$title_class = 'long-title ';
	} else {
		if ( strlen($blogname) > 30 )
			$title_class = 'long-title ';
	}
	$title_class = $title_class.'w2oadm_topmenu';
	
	$url = trailingslashit( get_bloginfo('url') );
	
	$blog_title_parent_id = 'w2o_menu_site-name';
	
	if ($wp_w2oadm['wpicons']) {
		$w2oadm_customtag_toplevel = '<w2o class="w2oadm_customtag_toplevel">'.$blogname.'</w2o>';			
		$img = '<div class="wp-menu-image w2omenu_text_toplevel dashicons-before dashicons-admin-home">'.$w2oadm_customtag_toplevel.'</div>';
			
	} else {
		$img = $blogname;
	}
	
	$wp_admin_bar->add_menu(array('id' => $blog_title_parent_id, 'title' => __($img), 'href' => $url, 'meta'=>array('title' => __('Visit site'), 'class' => $title_class ) ));		
}


function wp_w2oadm_sanitize_id($url) {
	$url = preg_replace('/^customize.php\?return=.*$/', 'customize', $url);
	$url = preg_replace('/(&|&amp;|&#038;)?_wpnonce=([^&]+)/', '', $url);
	return str_replace(array('.php','.','/','?','='),array('','_','_','_','_'),$url);
}
 
// Set defaults
function wp_w2oadm_defaults() {
	return array(		
		'toplinks' => 1,
		'hidebubble' => 0,
		'wpicons' => 1,
		'wpiconsonly' => 0, // Set 1 to enable "ICON ONLY" feature. NOTE: Make sure the "wpicons" flag is also enabled (i.e. wpicons flag needs be 1 as well)
	);
}

// Read plugin options or set default values. Triggers by the admin_init hook
function wp_w2oadm_init() {
	global $wp_w2oadm;
	
	$defaults = wp_w2oadm_defaults();
    if (!count( (array) $wp_w2oadm )) {
		$wp_w2oadm = array();
		
	}
	// Allow plugins to modify the config
	$wp_w2oadm = apply_filters( 'w2oadm_init_config', array_merge( $defaults, $wp_w2oadm ) );
	
	wp_w2o_admin_color_scheme();
}

// Return plugin URL (SSL pref compliant) (with trailing slash)
function wp_w2oadm_pluginurl() {
	return plugin_dir_url( dirname(__FILE__) );
}

// For inserting inside admin section's <head>. Triggers by the admin_head hook.
function wp_w2oadm_head() {	
	wp_w2oadm_css();
	wp_w2oadm_dynamic_styles();	
	wp_w2oadmm_preloadjs();
}

// For inserting inside admin footer section. Triggers by the admin_footer hook.
function wp_w2oadm_footer() {	
	wp_w2oadm_js();
}

// To add CSS file(s)
function wp_w2oadm_css() {
	global $wp_w2oadm, $pagenow, $text_direction;
			
	$plugin = wp_w2oadm_pluginurl().'core/';
	// query params
	$query = array(
		'v' => W2OADM_VER,
		'p' => wp_make_link_relative( $plugin ),	
		'w' => $wp_w2oadm['wpicons'],
		'wo' => $wp_w2oadm['wpiconsonly'],
		'h' => $wp_w2oadm['hidebubble'],
		'd' => ($text_direction == 'rtl' ? 'right' : 'left'), // right-to-left locale?
	);
	$query = http_build_query($query);

	echo "<link rel='stylesheet' href='{$plugin}w2oadm.css.php?$query' type='text/css' media='all' w2oadm='shishir.adhikari' />\n
	<link rel='stylesheet' href='{$plugin}w2oadm.responsive.css.php?$query' type='text/css' media='all' w2oadm='shishir.adhikari' />\n
	";
}

function wp_w2oadmm_preloadjs () {
	global $wp_w2oadm;
	
	$w2oadm_ie_checks = '<script type="text/javascript">var W2OADM_IE8_OR_LOWER = false;</script>
	';
	$w2oadm_ie_checks .= '<!--[if lte IE 8]><script type="text/javascript">W2OADM_IE8_OR_LOWER = true;</script><![endif]-->	
	<!--[if IE 8]>
	<script type="text/javascript">		
		/* Making the IE8 browser support custom tag(s) used by the plugin */		
		els = ["w2o"];
		for(var i = 0; i < els.length; i++) {
			document.createElement(els[i]);
		}
	</script><![endif]-->
	';
	
	$w2oadm_wpiconsonly = '<script type="text/javascript"> var W2OADM_WPICONSONLY = 0;</script>
	';
	if($wp_w2oadm['wpicons']) {
		if($wp_w2oadm['wpiconsonly']) {
			$w2oadm_wpiconsonly = '<script type="text/javascript"> var W2OADM_WPICONSONLY = 1;</script>
			';	
		}
	}	
	echo $w2oadm_ie_checks.$w2oadm_wpiconsonly;
}
// to add JS file(s)
function wp_w2oadm_js() {
	global $wp_w2oadm;
	
	$plugin = wp_w2oadm_pluginurl().'core/';
	
	wp_register_script( 'w2oadmJS', $plugin.'js/w2oadm.js', array( 'jquery' ), W2OADM_VER, true );
	wp_enqueue_script( 'w2oadmJS' );
}

// To auto set the original colors of w2oadm menus icon on fly, even while clicking the 'Admin Color Scheme' options.
function wp_w2oadm_dynamic_styles() {
	global $wp_w2oadm, $_wp_admin_css_colors;
	if(count($_wp_admin_css_colors)>0) {
		$current_color_scheme = get_user_option( 'admin_color' );
		$strCSS = '		
				.admin-color-[~color_scheme_key~] #wpadminbar .w2oadm_topmenu .wp-menu-image::before {
					color:[~icon_colors_base~];
				}
				.admin-color-[~color_scheme_key~] #wpadminbar .w2oadm_topmenu.hover .wp-menu-image::before, .admin-color-[~color_scheme_key~] #wpadminbar .w2oadm_topmenu a.ab-item:hover .wp-menu-image::before {
					color:[~icon_colors_foucs~];
				}
				.admin-color-[~color_scheme_key~] #wpadminbar .w2oadm_topmenu .wp-menu-image .w2oadm_svg svg {
					fill:[~icon_colors_base~] !important;
					width:20px !important;
					height:20px !important;
					vertical-align:text-bottom !important;							
				}
				.admin-color-[~color_scheme_key~] #wpadminbar li.w2oadm_topmenu:hover .wp-menu-image .w2oadm_svg svg, .admin-color-[~color_scheme_key~] #wpadminbar w2oadm_topmenu .wp-menu-image .w2oadm_svg svg:hover, .admin-color-[~color_scheme_key~] #wpadminbar .w2oadm_topmenu a.ab-item:hover .wp-menu-image .w2oadm_svg svg {
					fill:[~icon_colors_foucs~] !important;			
				}					
			';
		// list of placeholders to be searched and replaced in the above CSS code	
		$arrSearch = array( '[~color_scheme_key~]','[~icon_colors_base~]','[~icon_colors_foucs~]' );
		
		echo '<style type="text/css" w2oadm="shishir.adhikari">
				/* w2oadm menus icon colors */
		';
					
		// It's possible to have a color scheme set that is no longer registered. So, addjusting the CSS (colors of menu, its icons etc) for that case with default WP color scheme known as "fresh".
		if ( !empty($current_color_scheme) && empty( $_wp_admin_css_colors[ $current_color_scheme ] ) ) {
			$default_color_scheme = 'fresh';
			$default_colors = $_wp_admin_css_colors[ $default_color_scheme ];
			echo (str_replace(
						$arrSearch, 
						array($current_color_scheme, $default_colors->icon_colors['base'], $default_colors->icon_colors['focus']), 
						$strCSS
						)
				 );	
		}
		// End of above mentioned case
		
		foreach ($_wp_admin_css_colors as $mainKey=>$secondaryKey) {
			echo (str_replace(
						$arrSearch, 
						array($mainKey, $secondaryKey->icon_colors['base'], $secondaryKey->icon_colors['focus']), 
						$strCSS
						)
				 );			
		}
		echo '</style>';
	}
}

// To add Admin Color Scheme called "W2O"
function wp_w2o_admin_color_scheme () {
	global $wp_w2oadm;
		
	$suffix = is_rtl() ? '-rtl' : '';
	wp_admin_css_color(
		'w2orange', __( 'W2O', 'admin_schemes' ),
		plugins_url( "admincolorscheme/w2orange/colors".$suffix.".css", __FILE__ ),
		array( '#ff9724', '#6fb61d', '#de5e60', '#ceee65' ),
		array( 'base' => '#f3f1f1', 'focus' => '#fff', 'current' => '#fff' )
	);
	
}
?>