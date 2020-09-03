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
 * @change 2020/06/24
 */
class Hosted
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
     * get oauth authorize url
     *
     * @param array $params
     *
     * @return string
     */
    public function getOAuthAuthorizeUrl(array $params): string
    {
        $params['client_id'] = $this->options->getClientApps()['client_id'];

        $rules = V::keySet(
            V::key('scopes', V::stringType()->notEmpty()),
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('redirect_uri', V::url()),
            V::key('response_type', V::in(['code', 'token'])),
            V::keyOptional('state', V::stringType()->length(1, 255)),
            V::keyOptional('login_hint', V::email())
        );

        V::doValidate($rules, $params);

        $query = \http_build_query($params, null, '&', PHP_QUERY_RFC3986);

        $apiUrl = \trim($this->options->getServer(), '/').API::LIST['oAuthAuthorize'];

        return \trim($apiUrl, '/').'?'.$query;
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth token
     *
     * @param string $code
     *
     * @return array
     */
    public function postOAuthToken(string $code): array
    {
        V::doValidate(V::stringType()->notEmpty(), $code);

        $params = $this->options->getClientApps();

        $params['code'] = $code;

        $query = ['grant_type' => 'authorization_code'];
        $query = \array_merge($query, $params);

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->post(API::LIST['oAuthToken']);
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth revoke
     *
     * @return array
     */
    public function postOAuthRevoke(): array
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setHeaderParams($header)
            ->post(API::LIST['oAuthRevoke']);
    }

    // ------------------------------------------------------------------------------
}
