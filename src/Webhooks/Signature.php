<?php

namespace Nylas\Webhooks;

use Throwable;
use Nylas\Utilities\Options;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Webhooks
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2022/01/27
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
     * echo challenge to validate webhook (for fpm mode)
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
     * get notification & parse it (for fpm mode)
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
        if ($vrif === false)
        {
            throw new NylasException(null, 'not a valid nylas request');
        }

        // parse notification data
        return $this->parseNotification($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * webhook X-Nylas-Signature header verification (for other mode)
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
        try
        {
            $data = \json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        }
        catch (Throwable)
        {
            // when not close the decode error
            if ($this->options->getAllOptions()['debug'])
            {
                $msg = 'Unable to parse response body into JSON: ';

                throw new NylasException(null, $msg.\json_last_error());
            }
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
