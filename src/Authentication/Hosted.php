<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
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
     * @var Request
     */
    private $request;

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
     * get oauth authorize
     *
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function getOAuthAuthorize(array $params)
    {
        $rules = V::keySet(
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('login_hint', V::email()),
            V::key('redirect_uri', V::url()),
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

        return $this->request->setQuery($query)->get(API::LIST['oAuthAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth token
     *
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function postOAuthToken(array $params)
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

        $query = ['grant_type' => 'authorization_code'];
        $query = array_merge($query, $params);

        return $this->request->setQuery($query)->post(API::LIST['oAuthToken']);
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth revoke
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Exception
     */
    public function postOAuthRevoke(string $accessToken)
    {
        if (!V::stringType()::notEmpty()->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->request->setHeaderParams($header)->post(API::LIST['oAuthRevoke']);
    }

    // ------------------------------------------------------------------------------

}
