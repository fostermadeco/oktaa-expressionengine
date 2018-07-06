<?php

namespace Oktaee;

class Extension extends Base
{
    public $settings_exist = 'n';

    public $settings = [];

    public $docs_url = 'https://developer.okta.com/quickstart/';

    public $name = OKTAEE_MODULE_NAME;

    public $version = OKTAEE_MODULE_VERSION;

    public $description = OKTAEE_MODULE_DESCRIPTION;

    public $themeUrl = '';

    /**
     * Extension constructor.
     * @param string $settings
     */
    public function __construct($settings = '')
    {
        $this->settings = $settings;
        // pass
    }


    /**
     * Extension settings form
     * @return array
     */
    public function settings()
    {
        $settings = [];

        return $settings;
    }


    /**
     * Install extension
     * @return bool
     */
    public function activate_extension()
    {
        $hooks = [
            'core_boot'
        ];

        foreach ($hooks as $hook) {
            $data = [
                'class' => $this->name . '_ext',
                'method' => $hook,
                'hook' => $hook,
                'settings' => '',
                'priority' => 10,
                'version' => $this->version,
                'enabled' => 'y',
            ];

            ee()->db->insert('extensions', $data);
        }

        return true;
    }

    /**
     * Disable extension
     * @return bool
     */
    public function disable_extension()
    {
        ee()->db->where('class', $this->name . '_ext')
            ->delete('extensions');

        return true;
    }

    /**
     * Update extension
     * @param string $current
     * @return bool
     */
    public function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version) {
            return false;
        }

        ee()->db->where('class', $this->name . '_ext')
            ->update('extensions', ['version' => $this->version]);

        return true;
    }


    /**
     *
     */
    public function core_boot()
    {
		// We don't want to allow access to the login screen to someone
		// who is already logged in.

		// Only run this boot requests in the control panel.
		if (REQ != 'CP') {
			return;
		}

		if (ee()->session->userdata('member_id') === 0 &&
			ee()->session->userdata('admin_sess') != 1 &&
            ee()->config->item('okta_enabled'))
		{

		    if(array_key_exists('error', $_REQUEST)){
		        $error = "Okta Login Error: " . PHP_EOL . $_REQUEST['error'];
		        if(array_key_exists('error_description', $_REQUEST)){
		            $error = $error . ' - ' . urldecode($_REQUEST['error_description']);
                }
		        show_error($error);
            } else {
                // callbacks from Okta return a state and code in the request.  In this case, process the login request
                if(array_key_exists('state', $_REQUEST) && array_key_exists('code', $_REQUEST)) {
                    $this->process_login();
                } else {
                    // user is not logged into EE.  Authorize them via Okta
                    $this->okta_authorize();
                }
            }
		}
    }



}