<?php

namespace Nylas\Webhooks;

use Nylas\Utilities\Options;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Webhooks
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/26
 */
class Signature
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

        exit($challenge);
    }

    // ------------------------------------------------------------------------------

    /**
     * get notification & parse it
     *
     * @return array
     *               [
     *               date        => Timestamp when the change occurred
     *               type	     => The trigger for this notification
     *               object	     => The changed object type
     *               object_data => Contains the changed object's object type, account_id, object id and an attributes sub-object
     *               ]
     */
    public function getNotification(): array
    {
        $data = \file_get_contents('php://input');
        $code = $_SERVER['HTTP_X_NYLAS_SIGNATURE'] ?? '';
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
        $conf = $this->options->getClientSecret();

        $hash = \hash_hmac('sha256', $data, $conf);

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
}
