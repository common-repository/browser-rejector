<?php
/*
	Plugin Name: Browser Rejector
	Plugin URI: http://celloexpressions.com/plugins/browser-rejector
	Description: Notify, or optionally block, site visitors that they are using an outdated web browser, so you can use html5, css3, etc. freely.
	Version: 3.0
	Author: Nick Halsey
	Author URI: http://celloexpressions.com
	License: GPL2
	Text Domain: browser-rejector
*/

/*  Copyright 2013  Nick Halsey (email : halseyns@gmail.com)

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

// load translations
function brejectr_loadtrans(){
	load_plugin_textdomain( 'browser-rejector', false, '/browser-rejector/lang/' );
}
add_action('plugins_loaded', 'brejectr_loadtrans');

// Set-up Action and Filter Hooks
if ( is_admin() ){
	register_activation_hook(__FILE__, 'brejectr_add_defaults');
	register_uninstall_hook(__FILE__, 'brejectr_delete_plugin_options');
	add_action('admin_init', 'brejectr_init' );
	add_action('admin_menu', 'brejectr_add_options_page');
	add_filter( 'plugin_action_links', 'brejectr_plugin_action_links', 10, 2 );
}

// Delete options table entries ONLY when plugin deactivated AND deleted
function brejectr_delete_plugin_options() {
	delete_option('brejectr_options');
}

// Define default option settings
function brejectr_add_defaults() {
	$tmp = get_option('brejectr_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('brejectr_options'); 
		$arr = array(	//rejection window
						"output" => "universal",
						"closeoption" => "yes",
						"bversions" => "latest",
						"headertext" => __('Did you know that your internet browser is out of date?', 'browser-rejector'),
						"p1text" => __('Your browser is out of date and may not be able to properly display our website.', 'browser-rejector'),
						"p2text" => __("A list of modern browsers and recent versions is below; simply click an icon to go to the browser's download page.", 'browser-rejector'),	
						
						"closemessage" => __('By closing this window you acknowledge that your experience on our website may be degraded.', 'browser-rejector'),
						"closelink" => __('Close Window', 'browser-rejector'),
						"closecookie" => "no",
						"closeie67" => '',
						
						//browsers to reject
						"msie5" => "1",
						"msie6" => "1",
						"msie7" => "1",
						"msie8" => "1",
						"msie" => "",
						
						"safari2-3" => "1",
						"safari4" => "",
						"safari" => "",
						
						"firefox1-2" => "1",
						"firefox3" => "1",
						"firefox" => "",
						
						"opera7-9" => "1",
						"opera10" => "1",
						"opera11" => "",
						"opera" => "",
						
						"chrome1-3" => "1",
						"chrome4-20" => "1",
						"chrome" => "",
						
						'custom-rejections' => '',
						'custom-blocks' => '',
						
						"chk_default_options_db" => ""
		);
		update_option('brejectr_options', $arr);
	}
}

// Init plugin options to white list our options
function brejectr_init(){
	register_setting( 'brejectr_plugin_options', 'brejectr_options', 'brejectr_validate_options' );
}

// Add menu page
function brejectr_add_options_page() {
	add_options_page('Browser Rejector Settings', __('Browser Rejector', 'browser-rejector'), 'manage_options', __FILE__, 'brejectr_render_form');
}

// Render the Plugin options form
function brejectr_render_form() {
	wp_enqueue_script('brejectr-admin-script', plugins_url('/brejectr-admin.js', __FILE__), array('jquery'));
	wp_enqueue_style('jreject-css', plugins_url('/jquery.reject.css', __FILE__));
	?>
	<!-- scripts and styles for the admin options page -->
	<style type="text/css">
		#brejectrsubpages { list-style-type: none; height: 32px; border-bottom: 1px solid #ababab; }
		.brejectrnav { float: left; border: 1px solid #ababab;  margin: 0 5px; }
		.brejectrnav h1 { font-size: 20px; padding: 2px 5px; line-height: 0; font-face: Arial, sans-serif; }
		.brejectrnav:hover, .brejectrnav:active { color: #21759b; cursor: pointer; }
		.brejectrcurrent { border-bottom: 1px solid #fff; color: #21759b; border-right: 1px solid #ababab; }
		.brejectrac {width: 500px; padding: 10px; font-size: 20px; font-weight: bold; font-family: Arial, sans-serif; background-color: #ababab; color: #21759b; border: 1px solid #ababab; }
		.brejectrac:hover {cursor: pointer; color: #11556b; border: 1px solid #eeeeee; }
		.brejectr_testimg {opacity: .7; filter:alpha(opacity=70); border-radius: 8px; width: 50px; } .brejectr_testimg:hover {opacity: 1; filter:alpha(opacity=100);}
		::selection { background-color: #000; color: #77f; text-shadow: none; }/*get rid of the ugly default blue, just for fun, and wordpress' text shadow (really???)*/
		a:hover { cursor: pointer; }
	</style>
	<div class="wrap">	
		<!-- Display Plugin Header, then the subpage navigation tabs -->
		<h2><?php _e('Browser Rejector Settings', 'browser-rejector'); ?></h2><?php // wp messages hook into the page content's first <h2>, so I can't use h1 here, so I do a modified h1 for the nav... ?>
		<ul id="brejectrsubpages">
			<li id="brejectrsuboptionspage_win" class="brejectrnav"><h1><?php _e('Rejection Window', 'browser-rejector'); ?></h1></li>
			<li id="brejectrsuboptionspage_tor" class="brejectrnav"><h1><?php _e('Browsers to Reject', 'browser-rejector'); ?></h1></li>
			<li id="brejectrsuboptionspage_preview" class="brejectrnav"><h1><?php _e('Preview', 'browser-rejector'); ?></h1></li>
		</ul>
		
		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('brejectr_plugin_options'); ?>
			<?php $options = get_option('brejectr_options'); 
			
			if(!array_key_exists('output',$options))
				$options['output'] = 'universal';
			if(!array_key_exists('custom-rejections',$options))
				$options['custom-rejections'] = '';
			if(!array_key_exists('custom-blocks',$options))
				$options['custom-blocks'] = '';
			
			?>

			<table class="form-table" id="brejectr_tor">
				<!-- IE -->
				<tr valign="top">
					<th scope="row"><?php _e('Internet Explorer', 'browser-rejector'); ?><br /><i>(<?php _e('Current Version', 'browser-rejector') ?>: 10)</i></th>
					<td>
						<label><input name="brejectr_options[msie6]" type="checkbox" value="1" <?php if (isset($options['msie6'])) { checked('1', $options['msie6']); } ?> /> <?php _e('6 (highly unpredictable and unstable)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[msie7]" type="checkbox" value="1" <?php if (isset($options['msie7'])) { checked('1', $options['msie7']); } ?> /> <?php _e('7 (very unpredictable)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[msie8]" type="checkbox" value="1" <?php if (isset($options['msie8'])) { checked('1', $options['msie8']); } ?> /> <?php _e('8 (no html5/css3 implementation)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[msie]" type="checkbox" value="1" <?php if (isset($options['msie'])) { checked('1', $options['msie']); } ?> /> <?php _e('All versions (discouraged except for testing purposes)', 'browser-rejector') ?></label>
					</td>	
				</tr>
				<!-- Firefox -->
				<tr valign="top">
					<th scope="row"><?php _e('Firefox', 'browser-rejector'); ?><br /><i>(<?php _e('Current Version', 'browser-rejector') ?>: 19)</i></th>
					<td>
						<label><input name="brejectr_options[firefox1-2]" type="checkbox" value="1" <?php if (isset($options['firefox1-2'])) { checked('1', $options['firefox1-2']); } ?> /> <?php _e('1-2 (no html5/css3)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[firefox3]" type="checkbox" value="1" <?php if (isset($options['firefox3'])) { checked('1', $options['firefox3']); } ?> /> <?php _e('3 (limited html5/css3, depending on 3.x version)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[firefox]" type="checkbox" value="1" <?php if (isset($options['firefox'])) { checked('1', $options['firefox']); } ?> /> <?php _e('All versions (discouraged except for testing purposes)', 'browser-rejector') ?></label>
					</td>	
				</tr>	
				<!-- Safari -->
				<tr valign="top">
					<th scope="row"><?php _e('Safari', 'browser-rejector'); ?><br /><i>(<?php _e('Current Version', 'browser-rejector') ?>: 6)</i></th>
					<td>
						<label><input name="brejectr_options[safari2-3]" type="checkbox" value="1" <?php if (isset($options['safari2-3'])) { checked('1', $options['safari2-3']); } ?> /> <?php _e('2-3 (no html5/css3)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[safari4]" type="checkbox" value="1" <?php if (isset($options['safari4'])) { checked('1', $options['safari4']); } ?> /> <?php _e('4 (some html5/css3) - <i>Note: Android 3.x default browser identifies as Safari 4', 'browser-rejector') ?></i></label><br />
						<label><input name="brejectr_options[safari]" type="checkbox" value="1" <?php if (isset($options['safari'])) { checked('1', $options['safari']); } ?> /> <?php _e('All versions (discouraged except for testing purposes)', 'browser-rejector') ?></label>
					</td>	
				</tr>
				<!-- Opera -->
				<tr valign="top">
					<th scope="row"><?php _e('Opera', 'browser-rejector'); ?><br /><i>(<?php _e('Current Version', 'browser-rejector') ?>: 12)</i></th>
					<td>
						<label><input name="brejectr_options[opera7-9]" type="checkbox" value="1" <?php if (isset($options['opera7-9'])) { checked('1', $options['opera7-9']); } ?> /> <?php _e('7-9 (no html5/css3)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[opera10]" type="checkbox" value="1" <?php if (isset($options['opera10'])) { checked('1', $options['opera10']); } ?> /> <?php _e('10 (early html5/css3 adoptions)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[opera]" type="checkbox" value="1" <?php if (isset($options['opera'])) { checked('1', $options['opera']); } ?> /> <?php _e('All versions (discouraged except for testing purposes)', 'browser-rejector') ?></label>
					</td>	
				</tr>	
				<!-- Chrome -->
				<tr valign="top">
					<th scope="row"><?php _e('Chrome', 'browser-rejector'); ?><br /><i>(<?php _e('Current Version', 'browser-rejector') ?>: 25)</i></th>
					<td>
						<label><input name="brejectr_options[chrome1-3]" type="checkbox" value="1" <?php if (isset($options['chrome1-3'])) { checked('1', $options['chrome1-3']); } ?> /> <?php _e('1-3 (early html5/css3 adoptions)', 'browser-rejector') ?></label><br />
						<label><input name="brejectr_options[chrome]" type="checkbox" value="1" <?php if (isset($options['chrome'])) { checked('1', $options['chrome']); } ?> /> <?php _e('All versions (discouraged except for testing purposes)', 'browser-rejector') ?></label>
					</td>	
				</tr>
				<!-- Custom -->
				<?php if(!array_key_exists('custom-rejections',$options))
					$options['custom-rejections'] = '';
				?>
				<tr valign="top">
					<th scope="row"><?php _e('Custom Browser Rejections', 'browser-rejector'); ?></th>
					<td>
						<p><?php _e('You can select additional, more obscure browsers to reject by entering their ids as a <b><em>comma-separated-list</em></b> below. Ids are generally the lowercase browser name, but are different in some cases. The ids are based on the browser\'s user agent string. Some examples are Firefox: "firefox", Google Chrome: "chrome", Internet Explorer: "msie". If you enter a browser name, all versions will be rejected. To select specific versions, include the (whole) version number after the id, with no space. For example, "msie6", "msie7", "chrome18", "firefox9" (do not include the quotation marks).'); ?></p>
						<textarea name="brejectr_options[custom-rejections]" ><?php echo $options['custom-rejections']; ?></textarea>
					</td>	
				</tr>
			</table>
				
			<!-- The Rejection Window Options -->
			<table class="form-table" id="brejectr_win">
				<!-- run browser rejector: -->
				<tr valign="top" >
					<th scope="row"><?php _e('Run Browser Rejector:', 'browser-rejector') ?><br /><i><?php _e('Note: the window overlays your site with a semi-transparent background.', 'browser-rejector') ?></i></th>
					<td>
						<label><input name="brejectr_options[output]" type="radio" value="universal" <?php checked('universal', $options['output']); ?> /> <?php _e('Universal', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('Browser Rejector will run a check on each page of your site', 'browser-rejector') ?></span></label><br />
						<!--<label><input name="brejectr_options[output]" type="radio" value="home" <?php checked('home', $options['output']); ?> /> <?php _e('Home', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('Browser Rejector will only run on your homepage, ', 'browser-rejector') ?></span></label><br />-->
						<label><input name="brejectr_options[output]" type="radio" value="custom" <?php checked('custom', $options['output']); ?> /> <?php _e('Custom', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('Put this code into your theme\'s template files for the pages where you want Browser Rejector to run: <<code>?php browser_rejector(); ?></code>', 'browser-rejector') ?></span></label><br />
					</td>
				</tr>
				<!-- allow visitors to access site	-->
				<tr valign="top" >
					<th scope="row"><?php _e('Allow Visitors to Close the Rejection Window?', 'browser-rejector') ?><br /><i><?php _e('Note: the window overlays your site with a semi-transparent background.', 'browser-rejector') ?></i></th>
					<td>
						<label><input name="brejectr_options[closeoption]" id="closeyes" type="radio" value="yes" <?php checked('yes', $options['closeoption']); ?> /> <?php _e('Yes', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('(visitors in rejected browsers can close the rejection message and continue to your site)', 'browser-rejector') ?></span></label><br />
						<label><input name="brejectr_options[closeoption]" id="closeno" type="radio" value="no" <?php checked('no', $options['closeoption']); ?> /> <?php _e('No', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('(visitors must install a modern broswer or Chrome Frame before accessing your site)', 'browser-rejector') ?></span></label><br />
					</td>
				</tr>
				<tr><td colspan="2"><b><?php _e("Make sure that your theme's header.php file has the necessary", 'browser-rejector'); ?> <<code><?php echo '?php wp_head(); ?</code>>'; ?></code> <?php _e('action hook.', 'browser-rejector') ?> <a href="http://codex.wordpress.org/Function_Reference/wp_head" target="_blank"><?php _e("Read WordPress' documentation on the wp_head() action hook here.", 'browser-rejector') ?></a></b></td></tr>
				<!-- latest version or x+?	-->
				<tr valign="top">
					<th scope="row"><?php _e('Browser Suggestion Display', 'browser-rejector'); ?></th>
					<td>
						<label><input name="brejectr_options[bversions]" type="radio" value="latest" <?php checked('latest', $options['bversions']); ?> /> <?php _e('Display latest browser version number', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('(plugin will be updated with each new browser version release, use if you keep your plugins up to date)', 'browser-rejector') ?></span></label><br />
						<label><input name="brejectr_options[bversions]" type="radio" value="plus" <?php checked('plus', $options['bversions']); ?> /> <?php _e('Display solid html5 version number+ ', 'browser-rejector') ?><span style="color:#666666;margin-left:9px;"><?php _e("(use if you don't update your plugins frequently)", 'browser-rejector') ?></span></label><br />
					</td>
				</tr>
				<!-- text areas for the rejection window -->
				<tr valign="top">
					<th scope="row"><?php _e('Header Text', 'browser-rejector') ?><br /><i><?php _e('NO DOUBLE QUOTES!!! (")', 'browser-rejector') ?></i></th>
					<td>
						<input type="text" size="100" name="brejectr_options[headertext]" value="<?php echo $options['headertext']; ?>" /><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Paragraph 1', 'browser-rejector') ?></th>
					<td>
						<input type="text" size="100" name="brejectr_options[p1text]" value="<?php echo $options['p1text']; ?>" /><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Paragraph 2', 'browser-rejector') ?></th>
					<td>
						<input type="text" size="100" name="brejectr_options[p2text]" value="<?php echo $options['p2text']; ?>" /><br />
					</td>
				</tr>
				
				<tr valign="top" id="closemessage" <?php if(brejectr_options_getOption('closeoption') == "no") { echo 'style="display:none"'; } ?>>
					<th scope="row"><?php _e('Close Message', 'browser-rejector') ?></th>
					<td>
						<input type="text" size="80" name="brejectr_options[closemessage]" value="<?php echo $options['closemessage']; ?>" /><br />
					</td>
				</tr>
				<tr valign="top" id="closelink" <?php if(brejectr_options_getOption('closeoption') == "no") { echo 'style="display:none"'; } ?>>
					<th scope="row"><?php _e('Close Link Text', 'browser-rejector') ?></th>
					<td>
						<input type="text" size="80" name="brejectr_options[closelink]" value="<?php echo $options['closelink']; ?>" /><br />
					</td>
				</tr>
				
				<!-- close cookies -->
				<tr valign="top" id="closecookies" <?php if(brejectr_options_getOption('closeoption') == "no") { echo 'style="display:none"'; } ?>>
					<th scope="row"><?php _e('Remember if the visitor closed the rejection message?', 'browser-rejector') ?></th>
					<td>
						<label><input name="brejectr_options[closecookie]" type="radio" value="yes" <?php checked('yes', $options['closecookie']); ?> /> <?php _e('Yes', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('(the rejection window will only be displayed once per session)', 'browser-rejector') ?></span></label><br />
						<label><input name="brejectr_options[closecookie]" type="radio" value="no" <?php checked('no', $options['closecookie']); ?> /> <?php _e('No', 'browser-rejector') ?> <span style="color:#666666;margin-left:9px;"><?php _e('(the rejection window will be displayed every time a new page is visited)', 'browser-rejector') ?></span></label>
					</td>
				</tr>
				<!-- don't allow closing on IE6/7? -->
				<tr valign="top" id="closeie67" <?php if(brejectr_options_getOption('closeoption') == "no") { echo 'style="display:none"'; } ?>>
					<th scope="row"><?php _e('Some browsers are much worse than others...', 'browser-rejector') ?></th>
					<td>
						<h4><?php _e("Completely block access (don't show the close link) on the following browsers:", 'browser-rejector') ?></h4>
						<label><input name="brejectr_options[closeie6]" type="checkbox" value="1" <?php if (isset($options['closeie6'])) { checked('1', $options['closeie6']); } ?> /> <?php _e('Internet Explorer 6', 'browser-rejector') ?></label> 
						<label><input name="brejectr_options[closeie7]" type="checkbox" value="1" <?php if (isset($options['closeie7'])) { checked('1', $options['closeie7']); } ?> /> <?php _e('Internet Explorer 7', 'browser-rejector') ?></label> 
						<label><input name="brejectr_options[closeff3]" type="checkbox" value="1" <?php if (isset($options['closeff3'])) { checked('1', $options['closeff3']); } ?> /> <?php _e("Firefox 1, 2, 3.x (unfortunately it isn't possible to distinguish between 3.x versions)", 'browser-rejector') ?></label> 
					</td>
				</tr>
				<!-- custom no-close -->
				<tr valign="top">
					<th scope="row"><?php _e('Custom Blocked Browsers', 'browser-rejector'); ?></th>
					<td>
						<p><?php _e('You can select additional browser versions to block access on by entering their ids as a <b><em>comma-separated-list</em></b> below. Ids are generally the lowercase browser name, but are different in some cases. The ids are based on the browser\'s user agent string. Some examples are Firefox: "firefox", Google Chrome: "chrome", Internet Explorer: "msie". If you enter a browser name, all versions will be blocked. To select specific versions, include the (whole) version number after the id, with no space. For example, "msie6", "msie7", "chrome18", "firefox9" (do not include the quotation marks). No trailing comma!'); ?></p>
						<textarea name="brejectr_options[custom-blocks]" ><?php echo $options['custom-blocks']; ?></textarea>
					</td>	
				</tr>
				<!-- advanced/hidden options -->
				<tr valign="top" style="border-top:#dddddd 1px solid; display:none;" id="advanced">
					<th scope="row"><?php _e('Advanced Database Options (primarily for debugging)', 'browser-rejector') ?></th>
					<td>
						<label><input name="brejectr_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> <?php _e('Restore defaults upon plugin deactivation/reactivation', 'browser-rejector') ?></label>
						<br /><span style="color:#666666;margin-left:2px;"><?php _e('Only check this if you want to reset plugin settings upon plugin reactivation', 'browser-rejector') ?></span>
					</td>
				</tr>
			</table>
			
			<table class="form-table" id="brejectr_preview">
				<?php 
					if(brejectr_options_getOption('bversions') == 'latest') {
						$ieversion = '10';
						$chromeversion = '25';
						$ffversion = '19';
						$operaversion = '12';
						$safariversion = '6';
					}
					else {
						$ieversion = '10+';
						$chromeversion = '19+';
						$ffversion = '10+';
						$operaversion = '11+';
						$safariversion = '5+';
					}
				?>
				<tr><td><p><?php _e('Updates after saving options, not all browser options will be displayed at once - the suggested browsers vary based on OS (no Safari on Windows, no IE on Mac, Chrome Frame on IE only).', 'browser-rejector') ?></p></td></tr>
				<tr><td><p><?php _e('Classes are also added to the html element in every browser, wich allow targeting of css for specific browsers. The classes are os, browser, browser version, rendering engine, rendering engine version ', 'browser-rejector') ?>(ex. win chrome chrome21 webkit webkit5).</p></td></tr>
				<tr><td>
					<div id="jr_inner" style="min-width: 400px; width: auto; ">
						<h1 id="jr_header"><?php echo brejectr_options_getOption('headertext'); ?></h1>
						<p><?php echo brejectr_options_getOption('p1text'); ?></p>
						<p><?php echo brejectr_options_getOption('p2text'); ?></p>
						<ul>
							<li id="jr_chrome" style="background-image: url(<?php echo plugins_url('/browsers/background_browser.gif', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; ">
								<div class="jr_icon" style="background-image: url(<?php echo plugins_url('/browsers/browser_chrome.png', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; "></div>
								<div><a href="http://www.google.com/chrome/"><?php _e('Chrome', 'browser-rejector') ?> <?php echo $chromeversion; ?></a></div>
							</li>
							<li id="jr_msie" style="background-image: url(<?php echo plugins_url('/browsers/background_browser.gif', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; ">
								<div class="jr_icon" style="background-image: url(<?php echo plugins_url('/browsers/browser_msie.png', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; "></div>
								<div><a href="http://www.microsoft.com/windows/Internet-explorer/"><?php _e('Internet Explorer', 'browser-rejector') ?> <b style="color: #f00; background-color: #fff;"><?php echo $ieversion; ?></b></a></div>
							</li>
							<li id="jr_firefox" style="background-image: url(<?php echo plugins_url('/browsers/background_browser.gif', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; ">
								<div class="jr_icon" style="background-image: url(<?php echo plugins_url('/browsers/browser_firefox.png', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; "></div>
								<div><a href="http://www.mozilla.com/firefox/new"><?php _e('Firefox', 'browser-rejector') ?> <?php echo $ffversion; ?></a></div>
							</li>
							<li id="jr_opera" style="background-image: url(<?php echo plugins_url('/browsers/background_browser.gif', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; ">
								<div class="jr_icon" style="background-image: url(<?php echo plugins_url('/browsers/browser_opera.png', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; "></div>
								<div><a href="http://www.opera.com/download/"><?php _e('Opera', 'browser-rejector') ?> <?php echo $operaversion; ?></a></div>
							</li>
							<li id="jr_safari" style="background-image: url(<?php echo plugins_url('/browsers/background_browser.gif', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; ">
								<div class="jr_icon" style="background-image: url(<?php echo plugins_url('/browsers/browser_safari.png', __FILE__); ?>); background-attachment: scroll; background-color: transparent; background-position: 0% 0%; background-repeat: no-repeat no-repeat; "></div>
								<div><a href="http://www.apple.com/safari/"><?php _e('Safari', 'browser-rejector') ?> <?php echo $safariversion; ?></a></div>
							</li>
						</ul>
						<?php if (brejectr_options_getOption('closeoption')) { ?>
							<div id="jr_close">
								<a href="#"><?php echo brejectr_options_getOption('closelink'); ?></a>
								<p><?php echo brejectr_options_getOption('closemessage'); ?></p>
							</div>
						<?php } ?>
					</div>
				</td></tr>
			</table>
						
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes', 'browser-rejector') ?>" />
			</p>
		</form>

	</div>
	<?php	
} //options page

// Sanitize and validate input. Accepts an array, return a sanitized array.
function brejectr_validate_options($input) {
	
	return $input;
}

function brejectr_validator_getoption($theoption, $thearray) {
	//if this check isn't made, wordpress gets confused and 
	//sends an error message for each key whose value is null
	//so when "checkbox" options are unchecked, for example,
	//an error is thrown instead of false being returned.
	if(array_key_exists( $theoption, $thearray ))
		return $thearray[$theoption];
	else
		return false;
}

// Display a Settings link on the main Plugins page
function brejectr_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$brejectr_links = '<a href="'.get_admin_url().'options-general.php?page=browser-rejector%2Fbrowser-rejector.php">'.__('Settings', 'browser-rejector').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $brejectr_links );
	}

	return $links;
}

//provides an easier way to safely get options
function brejectr_options_getOption($the_option) {
	$options = get_option('brejectr_options');
	//if this check isn't made, wordpress gets confused and 
	//sends an error message for each key whose value is null
	//so when "checkbox" options are unchecked, for example,
	//an error is thrown instead of false being returned.
	if(array_key_exists( $the_option, $options ))
		return $options[$the_option];
	else
		return false;
}
function brejectr_tftostring($inputtf) {
	if($inputtf)
		return 'true';
	else
		return 'false';
}
	
//tell wordpress to call the output
add_action('wp_head', 'brejectr_head');
add_action('wp_enqueue_scripts', 'brejectr_scripts');
$brejectroutput = brejectr_options_getOption('output');
if($brejectroutput == 'custom'){
	// do nothing, let browser_rejector() be called manually as desired
}
/*elseif($brejectroutput == 'home'){
	browser_rejector('checkhome');
}*/
else{
	add_action('wp_footer', 'brejectr_foot'); // run it everywhere
}
function browser_rejector(){
	$brejectroutput = brejectr_options_getOption('output');
	if($brejectroutput == 'custom')
		brejectr_foot();
}

function brejectr_head() {
	if(brejectr_options_getOption('custom-blocks') != ''){
		$custombarr = explode(',',brejectr_options_getOption('custom-blocks'));
	}
	
	//hide the ability to close the rejection window, as deisred (when the sub-option is available)
	if (brejectr_options_getOption('custom-blocks')||brejectr_options_getOption('closeie6')||brejectr_options_getOption('closeie7')||brejectr_options_getOption('closeie67')||brejectr_options_getOption('closeff3')){echo '<style type="text/css">';}
	if (brejectr_options_getOption('closeie6')||brejectr_options_getOption('closeie67')){echo '.msie6 #jr_close{ display:none; }'; }
	if (brejectr_options_getOption('closeie7')||brejectr_options_getOption('closeie67')){echo '.msie7 #jr_close{ display:none; }'; }
	if (brejectr_options_getOption('closeff3')){echo'.firefox3 #jr_close{ display:none; }'; }
	if (brejectr_options_getOption('custom-blocks')){ 
		$str = '';
		foreach($custombarr as $browser)
			$str .= ' .' . trim($browser) . ' #jr_close,';
		echo substr($str,0,-1) . ' { display:none; }'; 
	}
	if (brejectr_options_getOption('custom-blocks')||brejectr_options_getOption('closeie6')||brejectr_options_getOption('closeie7')||brejectr_options_getOption('closeie67')||brejectr_options_getOption('closeff3')){echo '</style>';}
}
function brejectr_scripts() {
	wp_enqueue_script('jreject', plugins_url('jquery.reject.js', __FILE__), array( 'jquery' ), false);
	wp_enqueue_style('jreject', plugins_url('jquery.reject.css', __FILE__));
}
function brejectr_foot() {
	if(brejectr_options_getOption('bversions') == 'latest') {
		$ieversion = '10';
		$chromeversion = '25';
		$ffversion = '19';
		$operaversion = '12';
		$safariversion = '6';
	}
	else {
		$ieversion = '10+';
		$chromeversion = '19+';
		$ffversion = '10+';
		$operaversion = '11+';
		$safariversion = '5+';
	}
	if(brejectr_options_getOption('closeoption') == 'no')
		$closeoption = 'false';
	else
		$closeoption = 'true';
	if(brejectr_options_getOption('closecookie') == 'no')
		$closecookie = 'false';
	else
		$closecookie = 'true';
		
	//turn the custom rejections into an array
	if(brejectr_options_getOption('custom-rejections') != ''){
		$customarr = explode(',',brejectr_options_getOption('custom-rejections'));
	}

	?>
<!-- Browser Rejector Reject Hook -->
<script type="text/javascript">
jQuery(document).ready(function($){
	$.reject({
		reject: {
			safari2: <?php echo brejectr_tftostring(brejectr_options_getOption('safari2-3')); ?>, // Apple Safari 
			safari3: <?php echo brejectr_tftostring(brejectr_options_getOption('safari2-3')); ?>, 
			safari4: <?php echo brejectr_tftostring(brejectr_options_getOption('safari4')); ?>, 
			safari: <?php echo brejectr_tftostring(brejectr_options_getOption('safari')); ?>,
			chrome1: <?php echo brejectr_tftostring(brejectr_options_getOption('chrome1-3')); ?>, // Google Chrome (very old)
			chrome2: <?php echo brejectr_tftostring(brejectr_options_getOption('chrome1-3')); ?>,
			chrome3: <?php echo brejectr_tftostring(brejectr_options_getOption('chrome1-3')); ?>,
			chrome: <?php echo brejectr_tftostring(brejectr_options_getOption('chrome')); ?>,
			firefox1: <?php echo brejectr_tftostring(brejectr_options_getOption('firefox1-2')); ?>, // Mozilla Firefox
			firefox2: <?php echo brejectr_tftostring(brejectr_options_getOption('firefox1-2')); ?>,
			firefox3: <?php echo brejectr_tftostring(brejectr_options_getOption('firefox3')); ?>,
			firefox: <?php echo brejectr_tftostring(brejectr_options_getOption('firefox')); ?>,
			msie5: true, // Microsoft Internet Explorer
			msie6: <?php echo brejectr_tftostring(brejectr_options_getOption('msie6')); ?>,
			msie7: <?php echo brejectr_tftostring(brejectr_options_getOption('msie7')); ?>,
			msie8: <?php echo brejectr_tftostring(brejectr_options_getOption('msie8')); ?>,
			msie: <?php echo brejectr_tftostring(brejectr_options_getOption('msie')); ?>,
			opera7: <?php echo brejectr_tftostring(brejectr_options_getOption('opera7-9')); ?>, // Opera
			opera8: <?php echo brejectr_tftostring(brejectr_options_getOption('opera7-9')); ?>,
			opera9: <?php echo brejectr_tftostring(brejectr_options_getOption('opera7-9')); ?>,
			opera10: <?php echo brejectr_tftostring(brejectr_options_getOption('opera10')); ?>,
			opera: <?php echo brejectr_tftostring(brejectr_options_getOption('opera')); ?>,
			konqueror1: true, // Konqueror (Linux) - not included in plugin options for simplicity
			konqueror2: true,
			konqueror3: true,
			<?php // custom options
			if(brejectr_options_getOption('custom-rejections')){
				foreach($customarr as $browser){
					echo trim($browser) . ': true, //custom
					';
				}
			}
			?>
			unknown: false // Everything else
		},
		header: "<?php echo brejectr_options_getOption('headertext'); ?>", // header
		paragraph1: "<?php echo brejectr_options_getOption('p1text'); ?>", // Paragraph 1
		paragraph2: "<?php echo brejectr_options_getOption('p2text'); ?>", // Paragraph 2
		close: <?php echo $closeoption; ?>,
		closeLink: "<?php echo brejectr_options_getOption('closelink'); ?>", // Close link text
		closeMessage: "<?php echo brejectr_options_getOption('closemessage'); ?>", // Message below close window link
		closeCookie: <?php echo $closecookie; ?>, // Set cookie to remmember close for this session
		display: ['chrome','gcf','msie','firefox','opera','safari'], //turns out ie9+ beats firefox in capabilities and overall features, so suggest it first; safari after Opera because of it's highly minimal OS support (latest OSX only)
		browserInfo: { // Settings for which browsers to display
			firefox: {
				text: '<?php _e('Firefox', 'browser-rejector') ?> ' + '<?php echo $ffversion; ?>', // Text below the icon  
				url: 'http://www.mozilla.org/firefox/new' // URL For icon/text link  
			},
			safari: {
				text: '<?php _e('Safari', 'browser-rejector') ?> ' + '<?php echo $safariversion; ?>',  
				url: 'http://www.apple.com/safari/'
			},
			opera: {
				text: '<?php _e('Opera', 'browser-rejector') ?> ' + '<?php echo $operaversion; ?>',  
				url: 'http://www.opera.com/download/'  
			},
			chrome: {
				text: '<?php _e('Chrome', 'browser-rejector') ?> ' + '<?php echo $chromeversion; ?>',  
				url: 'http://www.google.com/chrome/'
			},  
			msie: {
				text: '<?php _e('Internet Explorer', 'browser-rejector') ?> <b style="color: #00f; background-color: #fff;">' + '<?php echo $ieversion; ?>' + '</b>',  
				url: 'http://www.microsoft.com/windows/internet-explorer/'
			},
			gcf: {
				text: "<?php _e('Google Chrome Frame', 'browser-rejector') ?> <i><?php _e("(doesn't require admin)", 'browser-rejector') ?></i>",
				url: 'http://google.com/chromeframe/',
				// This browser option will only be displayed for MSIE
				allow: { all: false, msie: false }
			}
		},
		imagePath: '<?php echo plugins_url('/browsers/', __FILE__); // Path where images are located ?>'
	});	
});
</script>
<?php
}
?>