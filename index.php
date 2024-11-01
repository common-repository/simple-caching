<?php
/*  
Plugin Name:  Simple permanent assets & files cache 
Description: Cache images,scripts,styles permanently in a visitor's Browser...  (P.S.  OTHER MUST-HAVE PLUGINS FOR EVERYONE: http://bitly.com/MWPLUGINS  )
Author: tazotodua
Version: 1.1
LICENCE: Free
Author URI: http://www.protectpages.com/profile
Plugin URI: http://www.protectpages.com/
Donate link: http://paypal.me/tazotodua
*/
define('version__SFC', 1.1);

 //define essentials
define('only_mainsite__SFC', true);
define('domainURL__SFC',				(((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') 
											|| $_SERVER['SERVER_PORT']===443) ? 
											'https://':'http://' ).$_SERVER['HTTP_HOST']);

define('wpURL__SFC',					(is_multisite() ? network_home_url() : home_url())  ); 
define('PLUGIN_URL_nodomain__SFC',		str_ireplace(domainURL__SFC, '', plugin_dir_url(__FILE__)) );
define('PLUGIN_DIR__SFC',				plugin_dir_path(__FILE__) );
//others
define('Optname__SFC',		'Opts__SFC');
define('pluginpage__SFC',	'simple-file-cache-sfc');
define('plugin_settings_page__SFC', 	(is_multisite() ? network_admin_url('settings.php') : admin_url( 'options-general.php') ). '?page='.pluginpage__SFC  );
define('ContactMeUrl__SFC',	'http://j.mp/contactmewordpresspluginstt');
define('NonceSlug__SFC',	'_my_nonc35225');
define('HtaccessSlugStart__SFC',	"##WP_TT_START (dont remove this block, you can disable this from dashboard)\r\n");
define('HtaccessSlugEnd__SFC',		"\r\n##WP_TT_END (dont remove this block, you can disable this from dashboard)");
define('WP_HTACCESS_SLUG__SFC',		"# BEGIN WordPress");
define('htaccess__SFC', ABSPATH.'.htaccess');
define('RenameNotice__SFC', 'p.s. Remember! dont worry, in case you meet problems, enter your FTP and just replace  <b>.htaccess</b> with <b>.htaccess_BACKUP</b> from there.');

	if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

// neccessary funtions
	function RandomString__SFC($length = 10) {
		return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
	}
	
	function get_mainsite_options__SFC($keyname=false){ 
		if(is_multisite()){
			$x = get_site_option(Optname__SFC, array());  if($keyname) { $x= array_key_exists($keyname,$x) ? $x[$keyname] : ''; }
		}
		else{$x = get_site_options__SFC($keyname); }
		return $x;
	} 

	function get_site_options__SFC($keyname=false){ 
		$x = get_site_option(Optname__SFC, array());  if($keyname) { $x= array_key_exists($keyname,$x) ? $x[$keyname] : ''; }
		return $x;
	} 
	function update_mainsite_options__SFC($x,$y){ return (is_multisite() ? update_site_option($x,$y) : update_site_option($x,$y)); }
	function update_site_option_for_this_site__SFC($x,$y){return (only_mainsite__SFC ? update_site_option($x,$y) : update_site_option($x,$y) );}
	function get_optSFC_for_this_site__SFC($keyname){return (only_mainsite__SFC ? get_mainsite_options__SFC($keyname) : get_site_options__SFC($keyname) );}
	function get_site_option_for_this_site__SFC($keyname){return (only_mainsite__SFC ? get_site_option($keyname) : get_site_option($keyname) );}

//CHECK IF USER IS ADMIN
	function iss_admiiiiiin__SFC()   	{require_once(ABSPATH.'wp-includes/pluggable.php'); return (current_user_can('manage_options')? true:false);}
	function iss_editorrr__SFC()   		{require_once(ABSPATH.'wp-includes/pluggable.php'); return (current_user_can('manage_categories')? true:false);}
	function iss_admiiiiiin_network__SFC(){require_once(ABSPATH.'wp-includes/pluggable.php'); return (current_user_can('manage_network')? true:false);}
	function die_if_not_admin__SFC()	{if(!iss_admiiiiiin__SFC()) {die('not adminn_error_755 '.$_SERVER["SCRIPT_FILENAME"]);}	}
	
//CHECK IF USER CAN MODIFY OPTIONS PAGE
	function NonceCheckk__SFC($name='nonce_input_name', $action_name='blabla')  {
		return ( wp_verify_nonce($_POST[NonceSlug__SFC], $action_name)  ?  true : die("not allowed, refresh page!") );
	}
	function NonceFieldd__SFC($name='nonce_input_name', $action_name='blabla')  { return '<input type="hidden" name="'.NonceSlug__SFC.'" value="'.wp_create_nonce($action_name).'" />';}
	function Get_Htaccess__SFC(){ 
		if (!file_exists(htaccess__SFC)){file_put_contents(htaccess__SFC,"# BEGIN WordPress\r\n\r\n# END WordPress");}
		return file_get_contents(htaccess__SFC);
	}
	function Get_Htaccess_saved__SFC(){ return get_site_option();	}
	function Get_MyHtaccess__SFC(){ return file_get_contents(__DIR__.'/.my_htaccess_codes.txt');	}



register_activation_hook( __FILE__, 'activation__SFC' ); 
function activation__SFC(){
	// die if not network (when MULTISITE )
    if ( is_multisite() && ! strpos( $_SERVER['REQUEST_URI'], 'wp-admin/network/plugins.php' ) ) {
		die ( __( '<script>alert("Activate this plugin only from the NETWORK DASHBOARD.");</script>') );
    }
	
	@copy(htaccess__SFC, htaccess__SFC.'_BACKUP_'.date('H-i_d-m-Y').RandomString__SFC(12));
}


//register_deactivation_hook( __FILE__, 'deactivation__SFC' ); 
add_action( (is_multisite() ? 'network_admin_menu' : 'admin_menu') , function() {add_submenu_page(   (is_multisite() ?  'settings.php' : 'options-general.php'), 'Simple Assets Cache','Simple Assets Cache', 'manage_options' ,  pluginpage__SFC, 'SFC__callback' ); 	} );
function SFC__callback(){
	$opts=get_mainsite_options__SFC();
	$htaccess_content= Get_Htaccess__SFC();
	if(isset($_POST[NonceSlug__SFC])){
		NonceCheckk__SFC();
		//$opts['compression']= isset($_POST['sac_opts']['compression']);
		//$opts['caching']	= isset($_POST['sac_opts']['caching']);
		$htaccess_content	= stripslashes($_POST['sac_opts']['htaccess']);
		update_mainsite_options__SFC(Optname__SFC, $opts);
		update_mainsite_options__SFC('htaccess_backup__SFC', $htaccess_content); 
	}
	$contains_myplugin=preg_match('/'.preg_quote(HtaccessSlugStart__SFC).'/si',$htaccess_content);
	if(!$contains_myplugin){
		$htaccess_content= str_replace(WP_HTACCESS_SLUG__SFC, "\r\n\r\n".HtaccessSlugStart__SFC.Get_MyHtaccess__SFC().HtaccessSlugEnd__SFC."\r\n\r\n".WP_HTACCESS_SLUG__SFC, $htaccess_content);
	}
	?> 
	<style>span.codee{background-color:#D2CFCF; padding:1px 3px; border:1px solid; font-family: Consolas;} </style>
	<div class="eachLine" style="margin: 40px 0 0 0;">
		<h2>Please Read!</h2>
		This plugin enables visitors' browsers to cache files (images, scripts, etc...) using <b>htaccess</b> method (doesnt matter if you know it or not). I have made this plugin, because some caching plugin (they are great!) sometimes fail to enable caching on several hostings (because of limitations, crossovers with other plugins or etc...). 
		<br/><br/><?php echo RenameNotice__SFC;?>
		<br/><br/><br/>* In case of problems, please, <a href="<?php echo ContactMeUrl__SFC;?>" target="_blank"> contact me </a>.
	</div>
	

	<form action="" method="POST" id="spc_form" target="_blank"> 
	<div>
	<!--
	<table>
	<tr><td>Enable compression</td><td><input type="checkbox" name="sac_opts[compression]" value="" /></td></tr>
	<tr><td>Enable caching</td><td><input type="checkbox" name="sac_opts[caching]" value="" /></td></tr>
	</table>
	-->
	The below codes will be used in htaccess (Be cautios if you want to modify this field!).
	However, before saving, click "PREVIEW" to test it. If it shows ERROR_PAGE, then you should not save it.  (to view this plugin's original htaccess codes, that was used initially, click <a href="<?php echo PLUGIN_URL_nodomain__SFC.'/.my_htaccess_codes.txt';?>" target="_blank"> here</a>)
	<br/>
	<textarea name="sac_opts[htaccess]" style="background: #fef3d6; width:100%;height:300px;"><?php echo $htaccess_content;?></textarea>
	
	</div>
	<input type="submit" value="SAVE" name="sfc_previeww"	id="sfc_previeww" style="display:none;" />
	<input type="submit" value="SAVE" name="sfc_savee"		id="sfc_savee" style="display:none;" />
	<?php echo NonceFieldd__SFC();?>
	</form>
	<button onclick="mysave__SFC();">SAVE</button>
	<button onclick="myprev__SFC();">PREVIEW</button>
	<script>
	function myprev__SFC(){
		//var form = document.getElementById("spc_form");
		document.getElementById("sfc_previeww").click();
	}
	function mysave__SFC(){
		var form = document.getElementById("spc_form");
		form.setAttribute("target","");
		document.getElementById("sfc_savee").click();
	}
	</script>
	<?php 
}

add_action('init','check_test__SFC',1);
function check_test__SFC(){
	if (isset($_POST['sfc_previeww'])){
		if (iss_admiiiiiin__SFC()){
			$path =PLUGIN_DIR__SFC.'/test/'; if (!file_exists($path)) {  mkdir($path, 0755, true); }
			file_put_contents($path.'/.htaccess', stripslashes($_POST['sac_opts']['htaccess']));
			file_put_contents($path.'/index.php', '<div style="color:green;text-align:center;"><h1>Congrats, It seems working!</h1><br/>'.RenameNotice__SFC.'</div>');
			header("location: ".plugin_dir_url(__FILE__).'test/index.php', true, 302); exit;
			//die('<script>window.location="'.plugin_dir_url(__FILE__).'test/index.php";</script>');
		}
	}
}



								

									
								//===========  links in Plugins list ==========//
								add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), function ( $links ) {   $links[] = '<a href="'.plugin_settings_page__SFC.'">Settings</a>'; $links[] = '<a href="http://paypal.me/tazotodua">Donate</a>';  return $links; } );
								//REDIRECT SETTINGS PAGE (after activation)
								add_action( 'activated_plugin', function($plugin ) { if( $plugin == plugin_basename( __FILE__ ) ) { exit( wp_redirect( plugin_settings_page__SFC.'&isactivation'  ) ); } } );
								
?>