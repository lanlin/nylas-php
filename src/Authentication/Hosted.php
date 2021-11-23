<?php

namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Hosted Authentication
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/11/23
 */
class Hosted
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Authenticate your user using Hosted Authentication
     *
     * @see https://developer.nylas.com/docs/api/#get/oauth/authorize
     *
     * @param array $params
     *
     * @return string
     */
    public function authenticateUser(array $params): string
    {
        $params['client_id'] = $this->options->getClientId();

        V::doValidate(V::keySet(
            V::key('scopes', V::stringType()->notEmpty()),
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('redirect_uri', V::url()),
            V::key('response_type', V::in(['code', 'token'])),
            V::keyOptional('state', V::stringType()->length(1, 255)),
            V::keyOptional('login_hint', V::email())
        ), $params);

        $query  = \http_build_query($params, null, '&', PHP_QUERY_RFC3986);
        $apiUrl = \trim($this->options->getServer(), '/').API::LIST['oAuthAuthorize'];

        return \trim($apiUrl, '/').'?'.$query;
    }

    // ------------------------------------------------------------------------------

    /**
     * Send authorization code. An access token will return as part of the response.
     *
     * @see https://developer.nylas.com/docs/api/#post/oauth/token
     *
     * @param string $code
     *
     * @return array
     */
    public function sendAuthorizationCode(string $code): array
    {
        V::doValidate(V::stringType()->notEmpty(), $code);

        $params = [
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'client_id'     => $this->options->getClientId(),
            'client_secret' => $this->options->getClientSecret(),
        ];

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->post(API::LIST['oAuthToken']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Revoke access tokens.
     *
     * @see https://developer.nylas.com/docs/api/#post/oauth/revoke
     *
     * @return array
     */
    public function revokeAccessTokens(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['oAuthRevoke']);
    }

    // ------------------------------------------------------------------------------
}
