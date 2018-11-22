<?php namespace Nylas\Authentication;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
 */
class Account
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
     * get account info
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getAccountInfo(string $accessToken)
    {
        if (!V::stringType()::notEmpty()->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header =
        [
            'access_token' => $accessToken,
        ];

        return $this->request->setHeaderParams($header)->get(API::LIST['account']);
    }

    // ------------------------------------------------------------------------------

}
