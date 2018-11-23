<?php namespace Nylas\Webhooks;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Webhooks
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Webhook
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
     * get webhook list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getWebhookList(array $params)
    {
        $rules = V::keySet(
            V::key('client_id', V::stringType()::notEmpty()),
            V::key('client_secret', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['client_id']];
        $header = ['Authorization' => $params['client_secret']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getWebhook(array $params)
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
        ->get(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

}
