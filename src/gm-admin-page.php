<?php

namespace GraphMailer;

require_once 'gm-settings.php';
require_once 'gm-provider.php';

if (isset($_POST['graph_mailer_submit'])) {
    check_admin_referer('graph_mailer_configuration');
    GMSettings::update_option('tenant_id', $_POST['tenant_id'] ?? '');
    GMSettings::update_option('application_id', $_POST['application_id'] ?? '');
    GMSettings::update_option('secret', $_POST['secret'] ?? '');
    GMSettings::update_option('from', $_POST['from'] ?? '');
}
else if (isset($_GET['code']) && ($_GET['state'] ?? '.' === GMSettings::get_transient('state'))) {
    $token = GMProvider::get_access_token($_GET['code']);
    if ($token) {
        GMSettings::update_option('token', $token);
        GMSettings::delete_transient('state');

        echo '
            <div class="notice notice-success is-dismissible">
                <p>Authentication Successful</p>
            </div>
        ';
    }
}

?>
<div class="wrap">
    <h1>Graph Mailer Settings</h1>

    <h2>Application Configuration</h2>
    <p>
        In Azure, go to "App registrations" &gt; "New registration", and include the callback URL below in the Redirect URI field on the application registration page. If you already have a registered application, you can add the URL below to the Redirect URIs list.
    </p>
    <p><strong>Callback URL:</strong></p>
    <input type="text" readonly value="<?= htmlentities(GMProvider::get_callback_url()) ?>" style="width: 100%;">

    <h2>API Configuration</h2>
    <p>
        you can find these values in the Azure Application Overview page. If you do not have a client secret configured, you will need to create a new one.
    </p>
    <form method="POST">
        <?php wp_nonce_field('graph_mailer_configuration'); ?>
        <p>
            <label>
                <strong>Directory (tenant) ID:</strong><br>
                <input type="text" name="tenant_id" value="<?= GMSettings::get_option('tenant_id', '') ?>" style="width: 100%;">
            </label>
        </p>
        <p>
            <label>
                <strong>Application (client) ID:</strong><br>
                <input type="text" name="application_id" value="<?= GMSettings::get_option('application_id', '') ?>" style="width: 100%;">
            </label>
        </p>
        <p>
            <label>
                <strong>Secret:</strong><br>
                <input type="password" name="secret" value="<?= GMSettings::get_option('secret', '') ?>" style="width: 100%;">
            </label>
        </p>

    <?php if (
        !empty(GMSettings::get_option('tenant_id'))
        && !empty(GMSettings::get_option('application_id'))
        && !empty(GMSettings::get_option('secret'))
    ): ?>
        <p>
            <a class="button" href="<?= GMProvider::get_authorization_url() ?>">
                Authorize Now
            </a>
        <?php if (GMProvider::is_authorized()): ?>
            <p class="text-success">
                <span class="dashicons dashicons-yes-alt"></span> Graph Mailer is authorized
            </p>
        <?php endif; ?>
        </p>
    <?php endif; ?>

        <h2>Plugin Settings</h2>
        <p>
            <label>
                <strong>Send As:</strong><br>
                <input type="text" name="from" value="<?= GMSettings::get_option('from', '') ?>" style="width: 100%;">
            </label>
        </p>
        <p>
            <button class="button button-primary" type="submit" name="graph_mailer_submit">Save</button>
        </p>
    </form>
</div>
