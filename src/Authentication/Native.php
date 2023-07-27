<?php

declare(strict_types = 1);

namespace Nylas\Authentication;

use function is_array;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Native Authentication
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Native
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Native constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Send Authorization
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/connect/authorize
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function sendAuthorization(array $params): array
    {
        $params['client_id'] = $this->options->getClientId();

        V::doValidate($this->getAuthRules($params), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['connectAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Exchange the Token
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/connect/token
     *
     * @param string $code
     *
     * @return array
     * @throws GuzzleException
     */
    public function exchangeTheToken(string $code): array
    {
        V::doValidate(V::stringType()::notEmpty(), $code);

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
     * Detect Provider
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/connect/detect-provider
     *
     * @param string $email
     *
     * @return array
     * @throws GuzzleException
     */
    public function detectProvider(string $email): array
    {
        V::doValidate(V::email(), $email);

        $params = [
            'client_id'     => $this->options->getClientId(),
            'client_secret' => $this->options->getClientSecret(),
            'email_address' => $email,
        ];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['connectProvider']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get auth validate rules
     *
     * @param array $params
     *
     * @return V
     */
    private function getAuthRules(array $params): V
    {
        $provider = $params['provider'] ?? 'imap';

        return match ($provider)
        {
            'nylas'     => $this->nylasProviderRule(),
            'gmail'     => $this->gmailProviderRule(),
            'graph'     => $this->graphProviderRule(),
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
     * some known providers rules
     *
     * @return V
     */
    private function knownProviderRule(): V
    {
        $settings = V::keySet(V::key('password', V::stringType()::notEmpty()));

        return $this->getBaseRules(['aol', 'yahoo', 'icloud', 'hotmail'], $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * outlook provider rules
     *
     * @return V
     */
    private function outlookProviderRule(): V
    {
        $settings = V::keySet(
            V::key('username', V::stringType()::notEmpty()),
            V::key('password', V::stringType()::notEmpty()),
            V::key('exchange_server_host', V::domain())
        );

        return $this->getBaseRules('outlook', $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * graph provider rule
     *
     * @return V
     */
    private function graphProviderRule(): V
    {
        $settings = V::keySet(
            V::key('redirect_uri', V::url()),
            V::key('microsoft_client_id', V::stringType()::notEmpty()),
            V::key('microsoft_client_secret', V::stringType()::notEmpty()),
            V::key('microsoft_refresh_token', V::stringType()::notEmpty()),
        );

        return $this->getBaseRules('graph', $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * office 365 provider rules
     *
     * @return V
     */
    private function office365ProviderRule(): V
    {
        $settings = V::keySet(
            V::key('redirect_uri', V::url()),
            V::key('microsoft_client_id', V::stringType()::notEmpty()),
            V::key('microsoft_client_secret', V::stringType()::notEmpty()),
            V::key('microsoft_refresh_token', V::stringType()::notEmpty()),
        );

        return $this->getBaseRules('office365', $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * unknown imap provider
     *
     * @return V
     */
    private function imapProviderRule(): V
    {
        $settings = V::keySet(
            V::key('imap_host', V::domain()),
            V::key('imap_port', V::intType()),
            V::key('imap_username', V::stringType()::notEmpty()),
            V::key('imap_password', V::stringType()::notEmpty()),
            V::key('smtp_host', V::domain()),
            V::key('smtp_port', V::intType()),
            V::key('smtp_username', V::stringType()::notEmpty()),
            V::key('smtp_password', V::stringType()::notEmpty()),
            V::key('ssl_required', V::boolType())
        );

        return $this->getBaseRules('imap', $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * gmail provider rule
     *
     * @return V
     */
    private function gmailProviderRule(): V
    {
        $settings = V::oneOf(
            V::keySet(
                V::key('google_client_id', V::stringType()::notEmpty()),
                V::key('google_client_secret', V::stringType()::notEmpty()),
                V::key('google_refresh_token', V::stringType()::notEmpty())
            ),
            V::keySet(V::key('service_account_json', V::keySet(
                V::key('type', V::stringType()::notEmpty()),
                V::key('project_id', V::stringType()::notEmpty()),
                V::key('private_key', V::stringType()::notEmpty()),
                V::key('private_key_id', V::stringType()::notEmpty()),
                V::key('client_id', V::stringType()::notEmpty()),
                V::key('client_email', V::email()),
                V::key('auth_uri', V::url()),
                V::key('token_uri', V::url()),
                V::key('client_x509_cert_url', V::url()),
                V::key('auth_provider_x509_cert_url', V::url()),
            )))
        );

        return $this->getBaseRules('gmail', $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * exchange provider rules
     *
     * @return V
     */
    private function exchangeProviderRule(): V
    {
        $settings = V::oneOf(
            V::keySet(
                V::key('username', V::stringType()::notEmpty()),
                V::key('password', V::stringType()::notEmpty()),
                V::key('exchange_server_host', V::domain())
            ),
            V::keySet(
                V::key('username', V::stringType()::notEmpty()),
                V::key('password', V::stringType()::notEmpty()),
                V::key('service_account', V::boolType())
            ),
            V::keySet(
                V::key('username', V::stringType()::notEmpty()),
                V::key('password', V::stringType()::notEmpty()),
                V::keyOptional('eas_server_host', V::domain())
            ),
            V::keySet(
                V::key('redirect_uri', V::url()),
                V::key('service_account', V::boolType()),
                V::key('microsoft_client_id', V::stringType()::notEmpty()),
                V::key('microsoft_client_secret', V::stringType()::notEmpty()),
                V::key('microsoft_refresh_token', V::stringType()::notEmpty()),
            ),
        );

        return $this->getBaseRules('exchange', $settings);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private function nylasProviderRule(): V
    {
        $scopes = V::oneOf(
            V::stringType()::notEmpty(),
            V::simpleArray(V::in([
                'email',
                'email.send',
                'email.drafts',
                'email.modify',
                'email.metadata',
                'email.read_only',
                'email.folders_and_labels',
                'calendar',
                'calendar.read_only',
                'calendar.free_busy',
                'room_resources.read_only',
                'contacts',
                'contacts.read_only',
            ]))
        );

        return V::keySet(
            V::key('name', V::stringType()::notEmpty()),
            V::key('email', V::stringType()),
            V::key('provider', V::equals('nylas')),
            V::key('settings', V::equals([])),
            V::key('client_id', V::stringType()::notEmpty()),
            V::keyOptional('scopes', $scopes),
            V::keyOptional('reauth_account_id', V::stringType()::notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @param array|string $provider
     * @param V            $settings
     *
     * @return V
     */
    private function getBaseRules(string|array $provider, V $settings): V
    {
        $scopes = V::oneOf(
            V::stringType()::notEmpty(),
            V::simpleArray(V::in([
                'graph',
                'email',
                'email.send',
                'email.drafts',
                'email.modify',
                'email.metadata',
                'email.read_only',
                'email.folders_and_labels',
                'calendar',
                'calendar.read_only',
                'calendar.free_busy',
                'room_resources.read_only',
                'contacts',
                'contacts.read_only',
            ]))
        );

        return V::keySet(
            V::key('name', V::stringType()::notEmpty()),
            V::key('provider', is_array($provider) ? V::in($provider) : V::equals($provider)),
            V::key('settings', $settings),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('email_address', V::email()),
            V::keyOptional('scopes', $scopes),
            V::keyOptional('reauth_account_id', V::stringType()::notEmpty())
        );
    }

    // ------------------------------------------------------------------------------
}
