<?php namespace Nylas\Webhooks;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Webhooks
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/20
 */
class Webhook
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     * echo chanllenge to validate webhook
     *
     * TIPS: you'd better use the output method from your framework.
     */
    public function echoChallenge() : void
    {
        $chanllenge = $_GET['chanllenge'] ?? null;

        if (empty($chanllenge)) { return; }

        header('Content-Type: text/html; charset=utf-8', true, 200);

        die($chanllenge);
    }

    // ------------------------------------------------------------------------------

    /**
     * get notification & parse it
     *
     * @return array
     * [
     *     date        => Timestamp when the change occurred
     *     type	       => The trigger for this notification
     *     object	   => The changed object type
     *     object_data => Contains the changed object's object type, account_id,
     *                    object id and an attributes sub-object
     * ]
     */
    public function getNotification() : array
    {
        $code = $_SERVER['HTTP_X_NYLAS_SIGNATURE'] ?? '';
        $data = file_get_contents('php://input');
        $vrif = $this->xSignatureVerification($code, $data);

        // check if valid
        if($vrif === false)
        {
            throw new NylasException('not a valid nylas request');
        }

        // parse notification data
        return $this->parseNotification($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * webhook X-Nylas-Signature header verification
     *
     * @link https://docs.nylas.com/reference#receiving-notifications
     *
     * @param string $code
     * @param string $data
     * @return bool
     */
    public function xSignatureVerification(string $code, string $data) : bool
    {
        $conf = $this->options->getClientApps();

        $hash = hash_hmac('sha256', $data, $conf['client_secret']);

        return $code === $hash;
    }

    // ------------------------------------------------------------------------------

    /**
     * parse notification data
     *
     * @param string $data
     * @return array
     * @throws \Nylas\Exceptions\NylasException
     */
    public function parseNotification(string $data) : array
    {
        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        // check deltas
        if (!isset($data['deltas']))
        {
            throw new NylasException('invalid data');
        }

        return $data['deltas'];
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @return array
     */
    public function getWebhookList() : array
    {
        $params = $this->options->getClientApps();

        $rules = V::keySet(
            V::key('client_id', V::stringType()->notEmpty()),
            V::key('client_secret', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $header = ['Authorization' => $params['client_secret']];

        return $this->options
        ->getSync()
        ->setPath($params['client_id'])
        ->setHeaderParams($header)
        ->get(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @param string $webhookId
     * @return array
     */
    public function getWebhook(string $webhookId) : array
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
        ->getSync()
        ->setPath($params['client_id'], $params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

}
