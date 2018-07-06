<?php

namespace Oktaee;

class Update
{
    public $name = OKTAEE_MODULE_NAME;

    public $version = OKTAEE_MODULE_VERSION;

    public $description = OKTAEE_MODULE_DESCRIPTION;

    public function __construct()
    {
        // Pass
    }

    public function install()
    {
        // pass
    }

    public function uninstall()
    {
        // pass
    }

    public function update($current = '')
    {
        if ($current == '' OR $current == $this->version) {
            return false;
        }

        return true;
    }
}