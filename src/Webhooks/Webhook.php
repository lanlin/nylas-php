<?php

namespace Nylas\Webhooks;

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
 * @change 2020/06/27
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
     * echo challenge to validate webhook
     *
     * TIPS: you'd better use the output method from your framework.
     */
    public function echoChallenge(): void
    {
        $challenge = $_GET['challenge'] ?? null;

        if (empty($challenge))
        {
            return;
        }

        \header('Content-Type: text/html; charset=utf-8', true, 200);

        die($challenge);
    }

    // ------------------------------------------------------------------------------

    /**
     * get notification & parse it
     *
     * @return array
     *               [
     *               date        => Timestamp when the change occurred
     *               type	       => The trigger for this notification
     *               object	   => The changed object type
     *               object_data => Contains the changed object's object type, account_id,
     *               object id and an attributes sub-object
     *               ]
     */
    public function getNotification(): array
    {
        $code = $_SERVER['HTTP_X_NYLAS_SIGNATURE'] ?? '';
        $data = \file_get_contents('php://input');
        $vrif = $this->xSignatureVerification($code, $data);

        // check if valid
        if (false === $vrif)
        {
            throw new NylasException(null, 'not a valid nylas request');
        }

        // parse notification data
        return $this->parseNotification($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * webhook X-Nylas-Signature header verification
     *
     * @see https://docs.nylas.com/reference#receiving-notifications
     *
     * @param string $code
     * @param string $data
     *
     * @return bool
     */
    public function xSignatureVerification(string $code, string $data): bool
    {
        $conf = $this->options->getClientApps();

        $hash = \hash_hmac('sha256', $data, $conf['client_secret']);

        return $code === $hash;
    }

    // ------------------------------------------------------------------------------

    /**
     * parse notification data
     *
     * @param string $data
     *
     * @throws \Nylas\Exceptions\NylasException
     *
     * @return array
     */
    public function parseNotification(string $data): array
    {
        $data = \json_decode($data, true, 512);
        $errs = JSON_ERROR_NONE !== \json_last_error();

        // when not close the decode error
        if ($errs && $this->options->getAllOptions()['debug'])
        {
            $msg = 'Unable to parse response body into JSON: ';

            throw new NylasException(null, $msg.\json_last_error());
        }

        // check deltas
        if (!isset($data['deltas']))
        {
            throw new NylasException(null, 'invalid data');
        }

        return $data['deltas'];
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @return array
     */
    public function getWebhookList(): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientApps()['client_id'])
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get webhook list
     *
     * @param string $webhookId
     *
     * @return array
     */
    public function getWebhook(string $webhookId): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientApps()['client_id'], $webhookId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

    /**
     * create a webhook
     *
     * @param array $data
     *
     * @return array
     */
    public function createWebhook(array $data): array
    {
        V::doValidate(V::keySet(
            V::key('state', V::in(['active', 'inactive'])),
            V::key('triggers', V::simpleArray()),
            V::key('callback_url', V::url())
        ), $data);

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientApps()['client_id'])
            ->setFormParams($data)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->post(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update webhook state
     *
     * @param string $webhookId
     * @param bool   $enable
     *
     * @throws \Nylas\Exceptions\NylasException
     *
     * @return array
     */
    public function updateWebhook(string $webhookId, bool $enable = true): array
    {
        $params = ['state' => $enable ? 'active' : 'inactive'];

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientApps()['client_id'], $webhookId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->put(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update webhook state
     *
     * @param string $webhookId
     *
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteWebhook(string $webhookId): void
    {
        $this->options
            ->getSync()
            ->setPath($this->options->getClientApps()['client_id'], $webhookId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->delete(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------
}
