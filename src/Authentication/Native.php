<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Native Authentication
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
 */
class Native
{

    // ------------------------------------------------------------------------------

    /**
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    private $providers =
    [
        'gmail', 'yahoo', 'exchange', 'outlook', 'imap', 'icloud', 'hotmail', 'aol'
    ];

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    // ------------------------------------------------------------------------------

    /**
     * connect token
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function postConnectToken(array $params)
    {
        $rules = V::keySet(
            V::key('code', V::stringType()::notEmpty()),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('client_secret', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        return $this->request->setFormParams($params)->post(API::LIST['connectToken']);
    }

    // ------------------------------------------------------------------------------

    /**
     * connect authorize
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function postConnectAuthorize(array $params)
    {
        $setting = $this->settingsRules($params);

        $rules = V::keySet(
            V::key('name', V::stringType()::notEmpty()),
            V::key('settings', $setting),
            V::key('provider', V::in($this->providers)),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('email_address', V::email()),
            V::key('reauth_account_id', V::stringType()::notEmpty(), false)
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        return $this->request->setFormParams($params)->post(API::LIST['connectAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * validate settings params
     *
     * @param array $params
     * @return \Respect\Validation\Validator
     */
    private function settingsRules(array $params)
    {
        $provider = $params['provider'] ?? 'imap';

        switch ($provider)
        {
            case 'aol':
            case 'yahoo':
            case 'icloud':
            case 'hotmail': return $this->knownProviderRule();

            case 'imap':     return $this->imapProviderRule();
            case 'gmail':    return $this->gmailProviderRule();
            case 'exchange': return $this->exchangeProviderRule();

            default: return $this->imapProviderRule();
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Respect\Validation\Validator
     */
    private function knownProviderRule()
    {
        return V::keySet(V::key('password', V::stringType()::notEmpty()));
    }

    // ------------------------------------------------------------------------------

    /**
     * gmail provider rule
     *
     * @return \Respect\Validation\Validator
     */
    private function gmailProviderRule()
    {
        return V::keySet(
            V::key('google_client_id', V::stringType()::notEmpty()),
            V::key('google_client_secret', V::stringType()::notEmpty()),
            V::key('google_refresh_token', V::stringType()::notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * exchange provider rules
     *
     * @return \Respect\Validation\Validator
     */
    private function exchangeProviderRule()
    {
        return V::keySet(
            V::key('username', V::stringType()::notEmpty()),
            V::key('password', V::stringType()::notEmpty()),
            V::key('eas_server_host', V::stringType()::notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     *
     *
     * @return \Respect\Validation\Validator
     */
    private function imapProviderRule()
    {
        return V::keySet(
            V::key('imap_host', V::stringType()::notEmpty()),
            V::key('imap_port', V::stringType()::notEmpty()),
            V::key('imap_username', V::stringType()::notEmpty()),
            V::key('imap_password', V::stringType()::notEmpty()),

            V::key('smtp_host', V::stringType()::notEmpty()),
            V::key('smtp_port', V::stringType()::notEmpty()),
            V::key('smtp_username', V::stringType()::notEmpty()),
            V::key('smtp_password', V::stringType()::notEmpty()),
            V::key('ssl_required', V::boolType())
        );
    }

    // ------------------------------------------------------------------------------

}
