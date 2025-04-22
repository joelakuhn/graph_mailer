<?php

namespace GraphMailer;

use \TheNetworg\OAuth2\Client\Provider\Azure;

require_once 'vendor/autoload.php';
require_once 'gm-settings.php';

class GMProvider {
    private static $provider = null;

    public static function get_callback_url() : string {
        if (GMSettings::is_network_active()) {
            return network_admin_url('/settings.php?page=graph-mailer');
        }
        else {
            return admin_url('/options-general.php?page=graph-mailer');
        }
    }

    public static function get_provider() {
        if (!is_null(self::$provider)) {
            return self::$provider;
        }

        $secret = GMSettings::get_option('secret');
        $tenant_id = GMSettings::get_option('tenant_id');
        $application_id = GMSettings::get_option('application_id');

        $provider = new Azure([
            'clientId'     => $application_id,
            'tenant'       => $tenant_id,
            'clientSecret' => $secret,
            'redirectUri'  => self::get_callback_url(),
            'scopes'       => [
                'openid',
                'profile',
                'email',
                'offline_access',
                'https://graph.microsoft.com/User.Read',
                'https://graph.microsoft.com/Mail.Send',
                'https://graph.microsoft.com/Mail.Send.Shared',
                'https://graph.microsoft.com/Mail.ReadWrite',
                'https://graph.microsoft.com/Mail.ReadWrite.Shared',
            ],
            'defaultEndPointVersion' => Azure::ENDPOINT_VERSION_2_0,
        ]);

        return self::$provider = $provider;
    }

    public static function get_token() {
        $token = GMSettings::get_option('token');
        if (!$token) return false;

        $provider = self::get_provider();
        if ($token->hasExpired()) {
            $token = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $token->getRefreshToken()
            ]);
            if ($token) {
                GMSettings::update_option('token', $token);
            }
        }

        return $token;
    }

    public static function get_authorization_url() {
        $provider = self::get_provider();

        $authUrl = $provider->getAuthorizationUrl();
        GMSettings::set_transient('state', $provider->getState());

        echo $authUrl;
    }

    public static function get_access_token($code) {
        $provider = self::get_provider();
        return $provider->getAccessToken('authorization_code', [ 'code' => $code ]);
    }

    public static function post($path, $data) {
        $provider = self::get_provider();
        $token = self::get_token();
        $root_uri = $provider->getRootMicrosoftGraphUri(null);

        if ($token && $provider) {
            return $provider->post($root_uri . $path, $data, $token);
        }
        return false;
    }

    public static function is_authorized() {
        return !empty(GMSettings::get_option('token'));
    }
}
