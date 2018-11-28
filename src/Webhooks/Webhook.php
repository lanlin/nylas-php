<?php namespace Nylas\Webhooks;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

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
     */
    public function getWebhookList()
    {
        $params = $this->options->getClientApps();

        $rules = V::keySet(
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $header = ['Authorization' => $params['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($params['client_id'])
        ->setHeaderParams($header)
        ->get(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @param string $webhookId
     * @return mixed
     */
    public function getWebhook(string $webhookId)
    {
        $params = $this->options->getClientApps();

        $params['id'] = $webhookId;

        $rules = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $header = ['Authorization' => $params['client_secret']];

        return $this->options
        ->getRequest()
        ->setPath($params['client_id'], $params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

}
