<?php

namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Native Authentication
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class Native
{
    // ------------------------------------------------------------------------------

    /**
     * @var array
     */
    private array $providers =
    [
        'gmail', 'yahoo', 'exchange', 'outlook', 'imap', 'icloud', 'hotmail', 'aol',
    ];

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     *
     * @return array
     */
    public function postConnectToken(string $code): array
    {
        V::doValidate(V::stringType()->notEmpty(), $code);

        $params = $this->options->getClientApps();

        $params['code'] = $code;

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['connectToken']);
    }

    // ------------------------------------------------------------------------------

    /**
     * connect authorize
     *
     * @param array $params
     *
     * @return array
     */
    public function postConnectAuthorize(array $params): array
    {
        $setting = $this->settingsRules($params);

        $params['client_id'] = $this->options->getClientApps()['client_id'];

        $rules = V::keySet(
            V::key('name', V::stringType()->notEmpty()),
            V::key('settings', $setting),
            V::key('provider', V::in($this->providers)),
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('email_address', V::email()),

            // @see https://docs.nylas.com/docs/authentication-scopes
            V::keyOptional('scopes', V::stringType()->notEmpty()),

            // re-authenticate existing account id
            V::keyOptional('reauth_account_id', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['connectAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * validate settings params
     *
     * @param array $params
     *
     * @return \Nylas\Utilities\Validator
     */
    private function settingsRules(array $params): V
    {
        $provider = $params['provider'] ?? 'imap';

        switch ($provider)
        {
            case 'aol':
            case 'yahoo':
            case 'icloud':
            case 'hotmail': return $this->knownProviderRule();

            case 'gmail':    return $this->gmailProviderRule();

            case 'exchange': return $this->exchangeProviderRule();

            case 'imap':
            default: return $this->imapProviderRule();
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Nylas\Utilities\Validator
     */
    private function knownProviderRule(): V
    {
        return V::keySet(V::key('password', V::stringType()->notEmpty()));
    }

    // ------------------------------------------------------------------------------

    /**
     * gmail provider rule
     *
     * @return \Nylas\Utilities\Validator
     */
    private function gmailProviderRule(): V
    {
        return V::keySet(
            V::key('google_client_id', V::stringType()->notEmpty()),
            V::key('google_client_secret', V::stringType()->notEmpty()),
            V::key('google_refresh_token', V::stringType()->notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * exchange provider rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function exchangeProviderRule(): V
    {
        return V::keySet(
            V::key('username', V::stringType()->notEmpty()),
            V::key('password', V::stringType()->notEmpty()),
            V::key('eas_server_host', V::stringType()->notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * unknown imap provider
     *
     * @return \Nylas\Utilities\Validator
     */
    private function imapProviderRule(): V
    {
        return V::keySet(
            V::key('imap_host', V::stringType()->notEmpty()),
            V::key('imap_port', V::stringType()->notEmpty()),
            V::key('imap_username', V::stringType()->notEmpty()),
            V::key('imap_password', V::stringType()->notEmpty()),
            V::key('smtp_host', V::stringType()->notEmpty()),
            V::key('smtp_port', V::stringType()->notEmpty()),
            V::key('smtp_username', V::stringType()->notEmpty()),
            V::key('smtp_password', V::stringType()->notEmpty()),
            V::key('ssl_required', V::boolType())
        );
    }

    // ------------------------------------------------------------------------------
}
