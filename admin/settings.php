<?php
if(isset($_POST['hash'])){
	UdimiOptin::saveUdimiCode($_POST);
	die();
}
if(isset($_POST['toggle_status'])){
    UdimiOptin::toggleStatus();
    die();
}

global $wpdb;
$ud_script = '';
$key = get_option('udimi_optin_key');
$name = get_option('udimi_optin_name');
$email = get_option('udimi_optin_email');
$connected = get_option('udimi_optin_connected', false);
$ud_message = $key ? '' : 'Plugin is not connected to Udimi. Log in to your Udimi.com account and click Connect button below';
$ud_message_class = 'error';
?>
<div class="wrap">
	<h2>Udimi Optin Settings</h2>

	<?php if (!empty($ud_message)): ?>
		<div class="updated settings-error <?= $ud_message_class; ?> is-dismissible">
			<p><?= $ud_message; ?></p>
			<button class="notice-dismiss" type="button">
				<span class="screen-reader-text">Dismiss this notice.</span>
			</button>
		</div>
	<?php endif;?>
    <?php if($key):?>
        <table class="form-table">
            <?php if($name):?>
                <tr>
                    <td>
                        Your Udimi account is <strong><?= $name ?></strong> <?= $email ? '('.$email.')' : ''?>
                    </td>
                </tr>
            <?php endif;?>
            <tr>
                <td>Status: <strong><?= $connected ? 'connected' : 'not connected'?></strong></td>
            </tr>
        </table>
        <button type="button" class="button button-primary" id="udimi-optin-status-button"><?php echo ($connected ? 'Disconnect' : 'Connect') ?></button>
    <?php else: ?>
        <button type="button" class="button button-primary" id="udimi-optin-connect-button"><?php echo ($key ? 'Reconnect' : 'Connect') ?></button>
    <?php endif;?>

</div>
