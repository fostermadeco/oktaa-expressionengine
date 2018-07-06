## Okta ExpressionEngine Control Panel Integration

### Overview:
This ExpressionEngine add-on circumvents the normal ExpressionEngine 
control panel screen, and instead forces the user to login via Okta in 
order to gain access to the EE control panel.

### Okta Parameters
We use the FocusLab config for out ExpressionEngine implementations.  When enabling this add-on,
the following variables should be defined and added to your config.  The Okta specific client id, secret,
and account url will need to be gathered from your Okta application.

$env_config['okta_enabled'] = true;         // true or false if we are implementing Okta auth.

$env_config['okta_client_id'] = 'xxxxxxxxxx';    // application client id

$env_config['okta_client_secret'] = 'xxxxxxxxxx';   // application client secret

$env_config['okta_base_url'] = 'https://example.okta.com';  // URL of okta api instance

$env_config['okta_state'] = 'abc123';  // random generated string.  this will be validated on callback

$env_config['okta_redirect_url'] = 'https://example.mysite.com/admin.php';  // referring admin url and redirect on okta login


### ExpressionEngine Settings
Please note, that in order for this add-on to function properly, under Security and Sessions
preferences, the 'Control Panel Session Type' must be set to Cookies Only.


### Dependencies
This add-on takes advantage of the ExpressionEngine core_boot extension hook.
This add-on was built for an EE v2.11 site, and the core_boot hook was hacked into the core.
It is untested on future EE versions.  The core_boot extension hook doesn't become
available until EE v3.5

### Okta Documentation
This add-on follows conventions described in this documentation:
https://developer.okta.com/quickstart/#/okta-sign-in-page/php/generic