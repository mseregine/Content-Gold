<?php
/*
Plugin Name: ContentGold
Plugin URI: http://www.socialgold.net
Description: blah
Version: The Plugin's Version Number, e.g.: 1.0
Author: Name Of The Plugin Author
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

function setup_paid_subscriber_role() {
  $role =& get_role('paid-subscriber');
  if( empty($role ) ) {
    add_role('paid-subscriber', 'Paid Subscriber');
    $role =& get_role('paid-subscriber');
    $role->add_cap('read');
    $role->add_cap('level_0');
    $role =& get_role('subscriber');
    $role->remove_cap('read');
  }
  }

function unsetup_paid_subscriber_role() {
  $role =& get_role('paid-subscriber');
  if( !empty($role ) ) {
    $role =& get_role('paid-subscriber');
    $role->remove_cap('read');
    $role->remove_cap('level_0');
    $role =& get_role('subscriber');
    $role->add_cap('read');
    remove_role('paid-subscriber', 'Paid Subscriber');
  }
  }



register_activation_hook( __FILE__, 'setup_paid_subscriber_role' );
register_deactivation_hook( __FILE__, 'unsetup_paid_subscriber_role' );


function contentgold_plugin_menu() {
  add_options_page('ContentGold Options', 'Content Gold Plugin', 'edit_plugins', 'contentgold-plugin-menu', 'contentgold_settings_page');

	//call register settings function
  add_action( 'admin_init', 'register_contentgold_settings' );
}


function register_contentgold_settings() {
	//register our settings
	register_setting( 'contentgold-settings-group', 'offer_id' );
	register_setting( 'contentgold-settings-group', 'merchant_secret' );
}

function contentgold_settings_page() {
?>

<div class="wrap">
<h2>Content Gold</h2>

<form method="post" action="options.php">
<?php settings_fields( 'contentgold-settings-group' ); ?>

<table class="form-table">

<tr valign="top">
<th scope="row">Offer Id</th>
<td><input type="text" name="offer_id" value="<?php echo get_option('offer_id'); ?>" /></td>
</tr>
 
<tr valign="top">
<th scope="row">Merchant Secret</th>
<td><input type="text" name="merchant_secret" value="<?php echo get_option('merchant_secret'); ?>" /></td>
</tr>

</table>

<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>

</form>

<?php
  $signature = '';
  $offer_id = get_option('offer_id');
  $merchant_secret = get_option('merchant_secret');
  if( $offer_id && $merchant_secret ) {
    display_subscription_frame();
  }
?>

</div>
<?php
}

add_action('admin_menu', 'contentgold_plugin_menu');
function display_subscription_frame() {
  $offer_id = get_option('offer_id');
  $merchant_secret = get_option('merchant_secret');
  $dude = wp_get_current_user();
  $user_id = $dude->ID;
  $ts = time();
  $sigparams = array(
		   "ts" => $ts,
		   "subscription_offer_id" => $offer_id,
		   "action" => "show_form",
		   "user_id" => $user_id
		   );
  $signature =_calculateSGSignature( $sigparams, $merchant_secret );
  if( $user_id == 0 ) {
    // TODO: Handle this!
  }
    
?>
  <iframe src="https://api.jambool.com/socialgold/subscription/v1/<?php echo $offer_id; ?>/<?php echo $user_id ?>/show_form?ts=<?php echo $ts ?>&sig=<?php echo $signature ?>" width="430" height="400" scrolling="no" style="border: 1px solid #ccc;"></iframe>
<?php
}

function _calculateSGSignature( $params, $secret ) {
   $keys = array_keys( $params );
   sort($keys);
   $str = "";

   foreach ($keys as $key) {
     $value = $params[$key];
     if (isset($value)) $str .= $key.$value;
   }
   $str .= $secret;
   $sig = md5($str);
   return $sig;
 }
?>


