jQuery(function(jQuery){
var W2ORANGE_ADM = window.W2ORANGE_ADM || {};
	
/* ======================================================================
SMALL SCREEN CHECK (especially for MOBILE SCREEN) FOR ADJUSTING THE MENU
======================================================================= */

W2ORANGE_ADM.small_screencheck = function(){
	var wp_admin_bar_menu_toggle_w2oadm = jQuery('li#wp-admin-bar-menu-toggle').css('display');
	var is_wp_screen_small_w2oadm = (wp_admin_bar_menu_toggle_w2oadm === "none") ? false : true;
	
	return is_wp_screen_small_w2oadm;
};

/* =======================================
ADJUSTING THE MENU BAR IF MENU OVERFLOWS
======================================= */

W2ORANGE_ADM.auto_adjust_menus = function(){
	var top_menu_width_sum = 0;
	var is_top_menu_floating_down = false;
	jQuery("#wp-toolbar > ul > li").each(function() { //select all top level li tags
		var str = parseInt(jQuery(this).innerWidth(), 10);
        top_menu_width_sum += (!isNaN(str) ? str : 0);
    });
	
	var toolbar_width =	(jQuery.browser.msie) ? jQuery(window).width() : parseInt(jQuery("#wp-toolbar > ul").innerWidth(), 10);
	if (top_menu_width_sum > toolbar_width) {
		var diff = top_menu_width_sum - toolbar_width; 
		if (diff > 20 ) {
			is_top_menu_floating_down = true;
		} else {
			is_top_menu_floating_down = false;
		}
	} else {
		is_top_menu_floating_down = false;
	}
	// Adjust dropdown menu, wpbody if foats down
	var w2oadm_wpbody = ~~(top_menu_width_sum / toolbar_width);
	w2oadm_wpbody = (w2oadm_wpbody <= 0) ? 1 : w2oadm_wpbody;
	w2oadm_wpbody = (w2oadm_wpbody * 32) + 'px';
	
	var w2oadm_wpadminbar = ~~(top_menu_width_sum / toolbar_width);
	w2oadm_wpadminbar = (w2oadm_wpadminbar <=0) ? 1 : w2oadm_wpadminbar;
	w2oadm_wpadminbar = w2oadm_wpadminbar+1;
	w2oadm_wpadminbar = (w2oadm_wpadminbar * 32) + 'px';
	
	if (is_top_menu_floating_down && !W2ORANGE_ADM.small_screencheck()) {	
		jQuery('#wpadminbar').addClass('w2oadm_wpadminbar');	
		jQuery('#wpadminbar').css('height',w2oadm_wpadminbar);	
		jQuery('#wpbody').css('padding-top', w2oadm_wpbody);	
		
		jQuery('#wp-admin-bar-top-secondary').removeClass('w2oadm_mobile_wp-admin-bar-top-secondary').addClass('w2oadm_wp-admin-bar-top-secondary');	
		jQuery('#wp-admin-bar-my-account .ab-sub-wrapper').addClass('w2oadm_wp-admin-bar-my-account_ab-sub-wrapper');	
		
	} else {
		jQuery('#wpadminbar').removeClass('w2oadm_wpadminbar');	
		jQuery('#wpadminbar').css('height','auto');
		jQuery('#wpadminbar').css('min-height','32px');	
		if(jQuery(window).width()<=480) {
			jQuery('#wpbody').css('padding-top','46px');				
		}
		else {
			jQuery('#wpbody').css('padding-top','0');
		}
		jQuery('#wp-admin-bar-top-secondary').removeClass('w2oadm_wp-admin-bar-top-secondary').addClass('w2oadm_mobile_wp-admin-bar-top-secondary');	
		jQuery('#wp-admin-bar-my-account .ab-sub-wrapper').removeClass('w2oadm_wp-admin-bar-my-account_ab-sub-wrapper');
	}
};

W2ORANGE_ADM.remove_parent_text_only = function(dom){
	jQuery(dom).each(function() {
		var tmp = jQuery(this).children().remove();
     	jQuery(this).text('').append(tmp);
	});
	
};

jQuery(document).ready(function() {
		
	jQuery(window).bind("load", W2ORANGE_ADM.auto_adjust_menus);
	jQuery(window).bind("resize", W2ORANGE_ADM.auto_adjust_menus);
	jQuery(window).bind("orientationchange", W2ORANGE_ADM.auto_adjust_menus);
	
	/* CHECKING IE <= 8 FOR "IOCNONLY" FEATURE. FOR IE BROWSER, ICONONLY FEATURE IS BEST APPLICABLE IN VERSION > 8 BECAUSE IE8 AND BELOW DOES NOT SUPPORT SVG ICONS/IMAGES THAT WORDPRESS OR MANY THIRD PARTY PLUGINS USES */
	if(W2OADM_IE8_OR_LOWER) { W2OADM_WPICONSONLY = 0;}
	if( W2OADM_WPICONSONLY > 0) {
		W2ORANGE_ADM.remove_parent_text_only('#wpadminbar #wp-toolbar > ul > li  w2o.w2oadm_customtag_toplevel');
		W2ORANGE_ADM.remove_parent_text_only('#wpadminbar #wp-toolbar > ul > li  w2o.w2oadm_customtag_toplevel_org');
		W2ORANGE_ADM.remove_parent_text_only('#wpadminbar #wp-toolbar > ul > li  w2o.w2oadm_customtag_toplevel_org span.ab-label:not(.awaiting-mod, .pending-count, .update-plugins)');
		
		/* Used only for my another WP plugin "W2O Football Fans Admin Color Schemes" */
		W2ORANGE_ADM.remove_parent_text_only('#wpadminbar #wp-toolbar > ul > li  w2o.w2oadm_customtag_toplevel> w2o[w2ofbfnacs]');
	}
	// Wrap the comment iconic dropdown menu's bubble inside a span to make it look better
	jQuery('#wpadminbar li#wp-admin-bar-comments span.awaiting-mod').wrapInner('<span class="w2oadm-pending-count"></span>');
	
	// Remove unnecessary links in the top right corner
	var w2oadm_menu_uselesslinks = jQuery('#user_info p').html();
	if (w2oadm_menu_uselesslinks) {			
		jQuery('#user_info').css('z-index','81');		
	}
	
	if(jQuery.browser.msie) { // note: this IE browser checking condition works if "jQuery Migrate" plugin is used
		// hiding the parent/closest LI of the respective "a" link to remove the big gap that appears in IE (especially in <= IE7)
		jQuery('.customize-support .w2oadm_menu_sublevel a[href="themes.php?page=custom-header"]').closest('li.w2oadm_menu_sublevel').css("display", "none");
		jQuery('.customize-support .w2oadm_menu_sublevel a[href="themes.php?page=custom-background"]').closest('li.w2oadm_menu_sublevel').css("display", "none");		
	}
	
});

});


