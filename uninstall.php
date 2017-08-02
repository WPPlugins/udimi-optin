<?php
//if uninstall not called from WordPress exit
if (!defined('WP_UNINSTALL_PLUGIN'))
exit();

$options = array(
    'udimi_optin_key',
    'udimi_optin_script',
    'udimi_optin_name',
    'udimi_optin_email',
    'udimi_optin_date',
);

foreach($options as $option){
    delete_option($option);
}