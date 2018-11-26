<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
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
     * @var array
     */
    private $providers =
    [
        'gmail', 'yahoo', 'exchange', 'outlook', 'imap', 'icloud', 'hotmail', 'aol'
    ];

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Native constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * connect token
     *
     * @param string $code
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function postConnectToken(string $code)
    {
        if (!V::stringType()::notEmpty()->validate($code))
        {
            throw new NylasException('invalid params');
        }

        $params = $this->options->getClientApps();

        $params['code'] = $code;

        return $this->options->getRequest()->setFormParams($params)->post(API::LIST['connectToken']);
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
            V::key('email_address', V::email()),
            V::key('reauth_account_id', V::stringType()::notEmpty(), false)
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $params['client_id'] = $this->options->getClientApps()['client_id'];

        return $this->options->getRequest()->setFormParams($params)->post(API::LIST['connectAuthorize']);
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
     * unknown imap provider
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
