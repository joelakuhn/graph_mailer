<?php

namespace GraphMailer;

if (!defined('ABSPATH')) return;

require_once 'gm-settings.php';

add_action('wp_loaded', function() {
  if (GMSettings::is_network_active()) {
    add_action('network_admin_menu', function() {
      add_submenu_page('settings.php', 'Graph Mailer', 'Graph Mailer', 'update_plugins', 'graph-mailer', function() {
        require_once('gm-admin-page.php');
      });
    });
  }
  else {
    add_action('admin_menu', function() {
      add_options_page('Graph Mailer', 'Graph Mailer', 'update_plugins', 'graph-mailer', function() {
        require_once('gm-admin-page.php');
      });
    });
  }
});
