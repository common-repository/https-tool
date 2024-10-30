<?php

/*
Plugin Name: HTTPS tool
Plugin URI: http://lmprod.de/https/
Description: Redirects clients to https version if they send "upgrade-insecure-requests" header and sends "Content-Security-Policy: upgrade-insecure-requests" header if on https
Author: Joern Schellhaas
Version: 0.1
Author URI: http://lmprod.de/
License: GPLv2 or later
*/

namespace de\lmprod;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class HTTPS {
	static function check() {
		if(is_ssl()) {
			add_filter('wp_headers', array('de\lmprod\HTTPS', 'modifyHeaders'));
		}
		else if(isset($_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS']) && $_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS'] && empty($_POST)) {
			$redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			wp_redirect($redirect) && exit;  // 302 redirect maybe is bad for SEO, but breaks less things if it goes wrong... See https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Upgrade-Insecure-Requests
		}
	}

	static function modifyHeaders($headers) {
		$headers["Content-Security-Policy"] = "upgrade-insecure-requests";
		return $headers;
	}
}

add_action('plugins_loaded', array('de\lmprod\HTTPS', 'check'));

/* EOF */
