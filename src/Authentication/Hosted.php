<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Hosted Authentication
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
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
     * get oauth authorize
     *
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function getOAuthAuthorize(array $params)
    {
        $params['client_id'] = $this->options->getClientApps()['client_id'];

        $rules = V::keySet(
            V::key('login_hint', V::email()),
            V::key('redirect_uri', V::url()),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('state', V::stringType()::length(1, 255), false)
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $query =
        [
            'scope'         => 'email',
            'response_type' => 'code',     // code for server side, token for client side
        ];

        $query = array_merge($query, $params);

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->get(API::LIST['oAuthAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth token
     *
     * @param string $code
     * @return mixed
     * @throws \Exception
     */
    public function postOAuthToken(string $code)
    {
        if (!V::stringType()::notEmpty()->validate($code))
        {
            throw new NylasException('invalid params');
        }

        $params = $this->options->getClientApps();

        $params['code'] = $code;

        $query = ['grant_type' => 'authorization_code'];
        $query = array_merge($query, $params);

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->post(API::LIST['oAuthToken']);
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth revoke
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Exception
     */
    public function postOAuthRevoke(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        if (!V::stringType()::notEmpty()->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->post(API::LIST['oAuthRevoke']);
    }

    // ------------------------------------------------------------------------------

}
