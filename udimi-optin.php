<?php
/*
Plugin Name: Udimi Optin Tracking
Plugin URI:  http://udimi.com
Description: This plugin adds an optin tracking script to your site pages.
Version:     1.3
Author:      Overseas Internet Services LP
Author URI:  http://udimi.com
*/
defined('ABSPATH') or die( 'Access denied' );

require_once(dirname(__FILE__).'/class.php');

$udimiOptin = new UdimiOptin();

$udimiOptin->updateScript();

add_action('wp_head',array($udimiOptin, 'addOptinScript'));

add_action('admin_menu', array($udimiOptin, 'addAdminMenu'));

add_action('admin_enqueue_scripts', array($udimiOptin, 'addAdminScript'));

