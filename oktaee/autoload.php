<?php

if (!defined('OKTAEE_MODULE_VERSION')) {
    define('OKTAEE_MODULE_VERSION', '1.0');
}

if (!defined('OKTAEE_MODULE_NAME')) {
    define('OKTAEE_MODULE_NAME', 'Oktaee');
}

if (!defined('OKTAEE_MODULE_DESCRIPTION')) {
    define('OKTAEE_MODULE_DESCRIPTION', 'Single Sign On Authentication against the Okta SSO / Multi-factor auth service');
}



require_once dirname(__FILE__) . '/vendor/autoload.php';