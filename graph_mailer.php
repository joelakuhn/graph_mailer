<?php
/**
 * Plugin Name:       Graph Mailer for Indiana Tech
 * Description:       Send email through Microsofts dumbass graph API instead of SMTP.
 */

namespace GraphMailer;

if (!defined('ABSPATH')) return;

if (is_admin()) {
    require_once 'src/gm-admin.php';
}

require_once 'src/gm-mailer.php';
