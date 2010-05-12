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



?>
