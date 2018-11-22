<?php namespace Nylas\Accounts;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account Manage
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
 */
class Manage
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
     * get accounts list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getAccountsList(array $params)
    {
        $rules = V::keySet(
            V::key('limit', V::intType()::min(1), false),
            V::key('offset', V::intType()::min(0), false),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('client_secret', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['client_id']];
        $header = ['Authorization' => $params['client_secret']];

        $pagination =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        return $this->request
        ->setPath($path)
        ->setQuery($pagination)
        ->setHeaderParams($header)
        ->get(API::LIST['listAllAccounts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get account info with client_secret
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getAccountInfo(array $params)
    {
        $rules = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('client_secret', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['client_id'], $params['id']];
        $header = ['Authorization' => $params['client_secret']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['listAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * re-active account with client_secret
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function reactiveAccount(array $params)
    {
        $rules = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('client_secret', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['client_id'], $params['id']];
        $header = ['Authorization' => $params['client_secret']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->post(API::LIST['reactiveAnAccount']);
    }

    // ------------------------------------------------------------------------------

    /**
     * cancel account with client_secret
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function cancelAccount(array $params)
    {
        $rules = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('client_secret', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['client_id'], $params['id']];
        $header = ['Authorization' => $params['client_secret']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->post(API::LIST['cancelAnAccount']);
    }

    // ------------------------------------------------------------------------------

}
