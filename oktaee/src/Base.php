<?php

namespace Oktaee;

class Base
{
    public function __construct()
    {
        $this->set_base_url();
    }

    /**
     * Sets base url for views
     *
     * @access     protected
     * @return     void
     */
    protected function set_base_url()
    {
        $this->base_url = $this->data['base_url'] = function_exists('cp_url')
            ? cp_url('addons_modules/show_module_cp', array('module' => 'oktaee'))
            : BASE . AMP . 'C=addons_modules&amp;M=show_module_cp&amp;module=oktaee';
    }


    /**
     * Redirects user to Okta to authorize login
     *
     * @access     protected
     * @return     void
     */
    protected function okta_authorize()
    {
        ee()->session->destroy();

        $query = http_build_query(
            [
                'client_id' => ee()->config->item('okta_client_id'),
                'response_type' => 'code',
                'response_mode' => 'query',
                'scope' => 'openid profile',
                'redirect_uri' => ee()->config->item('okta_redirect_url'),
                'state' => ee()->config->item('okta_state'),
                'nonce' => $this->uuid()
            ]
        );

        header('Location: ' . ee()->config->item('okta_base_url') . '/oauth2/v1/authorize?' . $query);

        exit();
    }

    protected function uuid(){
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected function process_login_action()
    {
        $redirect_action = '';
        $results = ee()->db->select('action_id')
            ->from('actions')
            ->where('class', $this->name)
            ->where('method', 'process_login')
            ->limit(1)
            ->get();

        if (!$results->num_rows()) {
            show_error(ee()->lang->line('missing_module'));
        } else {
            $redirect_action = $results->row()->action_id;
        }

        return rtrim(ee()->config->site_url(), '/').'?ACT='.(string)$redirect_action;
    }


    /**
     * Process Login
     *
     * Handles the Callback from Okta
     *
     * Will validate the data from the POST, and log in the user
     * if their credentials check out.
     *
     * @return void
     */
    protected function process_login()
    {
        $exchange = false;
        if(array_key_exists('state', $_REQUEST) && $_REQUEST['state'] !== ee()->config->item('okta_state')) {
            throw new \Exception('State does not match.');
        }

        if(array_key_exists('code', $_REQUEST)) {
            $exchange = $this->exchangeCode($_REQUEST['code']);
        }

        if(array_key_exists('error', $_REQUEST)) {
            throw new \Exception($_REQUEST['error']);
        }

        $user = false;

        if ($exchange){
            if (property_exists($exchange, 'access_token')){
                $response = $this->validateToken($exchange->access_token, 'access_token');
                if (property_exists($response, 'username')){
                    $user = $response->username;
                }
            }
        }


        if (! $user || $user == null || $user == "") {
            show_error(ee()->lang->line('unauthorized_access'));
        }

        // Find the user in the db
        $results = ee()->db->where('email', $user)
            ->from('members')
            ->limit(1)
            ->get();

        // Error if we can't find a result for the member.
        if (!$results->num_rows()) {
            show_error(ee()->lang->line('invalid_user'));
        }

        ee()->session->create_new_session($results->row('member_id'), true);
        ee()->functions->redirect(ee()->config->item('cp_url'));
    }


    protected function exchangeCode($code) {
        $authHeaderSecret = base64_encode( ee()->config->item('okta_client_id').':'.ee()->config->item('okta_client_secret') );
        $query = http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => ee()->config->item('okta_redirect_url')
        ]);
        $headers = [
            'Authorization: Basic ' . $authHeaderSecret,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: close',
            'Content-Length: 0'
        ];
        $url = ee()->config->item('okta_base_url') . '/oauth2/v1/token?' . $query;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_error($ch)) {
            $httpcode = 500;
        }
        curl_close($ch);
        return json_decode($output);
    }


    protected function validateToken($token, $token_type) {
        $authHeaderSecret = base64_encode( ee()->config->item('okta_client_id').':'.ee()->config->item('okta_client_secret') );
        $query = http_build_query([
            'token' => $token,
            'token_type_hint' => $token_type
        ]);
        $headers = [
            'Authorization: Basic ' . $authHeaderSecret,
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Connection: close',
            'Content-Length: 0'
        ];
        $url = ee()->config->item('okta_base_url') . '/oauth2/v1/introspect?' . $query;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        $output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_error($ch)) {
            $httpcode = 500;
        }
        curl_close($ch);
        return json_decode($output);
    }

}
