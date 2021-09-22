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
 * @change 2021/09/22
 */
class Native
{
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
     * Send Authorization
     *
     * @see https://developer.nylas.com/docs/api/#post/connect/authorize
     *
     * @param array $params
     *
     * @return array
     */
    public function sendAuthorization(array $params): array
    {
        $params['client_id'] = $this->options->getClientId();

        V::doValidate(V::keySet(
            V::key('name', V::stringType()->notEmpty()),
            V::key('provider', V::in(API::PROVIDERS)),
            V::key('settings', $this->settingsRules($params)),
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('email_address', V::email()),
            V::keyOptional('scopes', V::stringType()->notEmpty()),

            // re-authenticate existing account id
            V::keyOptional('reauth_account_id', V::stringType()->notEmpty())
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['connectAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Exchange the Token
     *
     * @see https://developer.nylas.com/docs/api/#post/connect/token
     *
     * @param string $code
     *
     * @return array
     */
    public function exchangeTheToken(string $code): array
    {
        V::doValidate(V::stringType()->notEmpty(), $code);

        $params = [
            'code'          => $code,
            'client_id'     => $this->options->getClientId(),
            'client_secret' => $this->options->getClientSecret(),
        ];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['connectToken']);
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

        return match ($provider)
        {
            'nylas'     => $this->nylasProviderRule(),
            'gmail'     => $this->gmailProviderRule(),
            'outlook'   => $this->outlookProviderRule(),
            'exchange'  => $this->exchangeProviderRule(),
            'office365' => $this->office365ProviderRule(),
            'aol', 'yahoo', 'icloud', 'hotmail' => $this->knownProviderRule(),

            // imap & others
            default => $this->imapProviderRule(),
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Nylas\Utilities\Validator
     */
    private function nylasProviderRule(): V
    {
        return V::equals([]);
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
     * outlook provider rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function outlookProviderRule(): V
    {
        return V::keySet(
            V::key('username', V::stringType()->notEmpty()),
            V::key('password', V::stringType()->notEmpty()),
            V::key('exchange_server_host', V::domain())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * office 365 provider rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function office365ProviderRule(): V
    {
        return V::keySet(
            V::key('microsoft_client_id', V::stringType()->notEmpty()),
            V::key('microsoft_client_secret', V::stringType()->notEmpty()),
            V::key('microsoft_refresh_token', V::stringType()->notEmpty()),
            V::key('redirect_uri', V::url())
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
            V::key('imap_host', V::domain()),
            V::key('imap_port', V::intType()),
            V::key('imap_username', V::stringType()->notEmpty()),
            V::key('imap_password', V::stringType()->notEmpty()),
            V::key('smtp_host', V::domain()),
            V::key('smtp_port', V::intType()),
            V::key('smtp_username', V::stringType()->notEmpty()),
            V::key('smtp_password', V::stringType()->notEmpty()),
            V::key('ssl_required', V::boolType())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * gmail provider rule
     *
     * @return \Nylas\Utilities\Validator
     */
    private function gmailProviderRule(): V
    {
        return V::oneOf(
            V::keySet(
                V::key('google_client_id', V::stringType()->notEmpty()),
                V::key('google_client_secret', V::stringType()->notEmpty()),
                V::key('google_refresh_token', V::stringType()->notEmpty())
            ),
            V::keySet(V::key('service_account_json', V::keySet(
                V::key('type', V::stringType()->notEmpty()),
                V::key('project_id', V::stringType()->notEmpty()),
                V::key('private_key_id', V::stringType()->notEmpty()),
                V::key('private_key', V::stringType()->notEmpty()),
                V::key('client_email', V::email()),
                V::key('client_id', V::stringType()->notEmpty()),
                V::key('auth_uri', V::url()),
                V::key('token_uri', V::url()),
                V::key('auth_provider_x509_cert_url', V::url()),
                V::key('client_x509_cert_url', V::url()),
            )))
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
        return V::oneOf(
            V::keySet(
                V::key('username', V::stringType()->notEmpty()),
                V::key('password', V::stringType()->notEmpty()),
                V::key('exchange_server_host', V::domain())
            ),
            V::keySet(
                V::key('username', V::stringType()->notEmpty()),
                V::key('password', V::stringType()->notEmpty()),
                V::key('service_account', V::boolType())
            ),
            V::keySet(
                V::key('microsoft_client_id', V::stringType()->notEmpty()),
                V::key('microsoft_client_secret', V::stringType()->notEmpty()),
                V::key('microsoft_refresh_token', V::stringType()->notEmpty()),
                V::key('redirect_uri', V::url()),
                V::key('service_account', V::boolType())
            ),
        );
    }

    // ------------------------------------------------------------------------------
}
