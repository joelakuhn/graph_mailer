<?php
/**
 * Plugin Name:       Graph Mailer
 * Description:       Send email through Microsofts dumbass graph API instead of SMTP.
 */

namespace GraphMailer;

if (!defined('ABSPATH')) return;

if (is_admin()) {
    require_once 'src/gm-admin.php';
}

require_once 'src/gm-mailer.php';
