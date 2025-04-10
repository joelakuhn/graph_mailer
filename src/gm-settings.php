<?php

namespace GraphMailer;

class GMSettings {
    private static $is_network_active = null;

    public static function is_network_active() : bool {
        if (is_bool(self::$is_network_active)) return self::$is_network_active;

        return self::$is_network_active = function_exists('is_plugin_active_for_network')
            && is_plugin_active_for_network('graph_mailer/graph_mailer.php');
    }

    public static function get_option($option, $default = false) {
        $option = '__graph_mailer_' . $option;
        if (self::is_network_active()) {
            return get_site_option($option, $default);
        }
        else {
            return get_option($option, $default);
        }
    }

    public static function update_option($option, $value) {
        $option = '__graph_mailer_' . $option;
        if (self::is_network_active()) {
            return update_site_option($option, $value);
        }
        else {
            return update_option($option, $value);
        }
    }

    public static function delete_option($option) {
        $option = '__graph_mailer_' . $option;
        if (self::is_network_active()) {
            return delete_site_option($option);
        }
        else {
            return delete_option($option);
        }
    }

    public static function get_transient($transient) {
        $transient = '__graph_mailer_' . $transient;
        if (self::is_network_active()) {
            return get_site_transient($transient);
        }
        else {
            return get_transient($transient);
        }
    }

    public static function set_transient($transient, $value, $expiration = 0) {
        $transient = '__graph_mailer_' . $transient;
        if (self::is_network_active()) {
            return set_site_transient($transient, $value, $expiration);
        }
        else {
            return set_transient($transient, $value, $expiration);
        }
    }


    public static function delete_transient($transient) {
        $transient = '__graph_mailer_' . $transient;
        if (self::is_network_active()) {
            return delete_site_transient($transient);
        }
        else {
            return delete_transient($transient);
        }
    }
}
