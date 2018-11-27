<?php namespace Nylas\Webhooks;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
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
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Webhook constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getWebhookList()
    {
        $params = $this->options->getClientApps();

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

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @param string $webhookId
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getWebhook(string $webhookId)
    {
        $params = $this->options->getClientApps();

        $params['id'] = $webhookId;

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

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

}
