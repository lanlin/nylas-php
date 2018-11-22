<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;

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
     * @param string      $clientId
     * @param string      $redirect
     * @param string|NULL $email
     * @param string|NULL $state
     * @return mixed
     * @throws \Exception
     */
    public function getOAuthAuthorize(string $clientId, string $redirect, string $email = null, string $state = null)
    {
        $query =
        [
            'scope'         => 'email',
            'state'         => $state,     // maximum length of this string is 255 characters
            'client_id'     => $clientId,
            'login_hint'    => $email,
            'redirect_uri'  => $redirect,
            'response_type' => 'code',     // code for server side, token for client side
        ];

        return $this->request->setQuery($query)->get(API::LIST['oAuthAuthorize']);
    }

    // ------------------------------------------------------------------------------

    /**
     * post oauth token
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $code
     * @return mixed
     * @throws \Exception
     */
    public function postOAuthToken(string $clientId, string $clientSecret, string $code)
    {
        $query =
        [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'grant_type'    => 'authorization_code',
            'code'          => $code,
        ];

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
        $header =
        [
            'access_token' => $accessToken,
        ];

        return $this->request->setHeaderParams($header)->post(API::LIST['oAuthRevoke']);
    }

    // ------------------------------------------------------------------------------

}
