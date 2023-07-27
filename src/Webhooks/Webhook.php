<?php

declare(strict_types = 1);

namespace Nylas\Webhooks;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Webhooks
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Webhook
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Webhook constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns all webhooks.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/a/client_id/webhooks
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAllWebhooks(): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates a webhook.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/a/client_id/webhooks
     *
     * @param array $data
     *
     * @return array
     * @throws GuzzleException
     */
    public function createAWebhook(array $data): array
    {
        V::doValidate(V::keySet(
            V::key('state', V::in(['active', 'inactive'])),
            V::key('triggers', V::simpleArray(V::in(API::TRIGGERS))),
            V::key('callback_url', V::url())
        ), $data);

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId())
            ->setFormParams($data)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->post(API::LIST['webhooks']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a webhook by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/a/client_id/webhooks/id
     *
     * @param string $webhookId
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnAWebhook(string $webhookId): array
    {
        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $webhookId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->get(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates a webhook by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#put-/a/client_id/webhooks/id
     *
     * @param string $webhookId
     * @param bool   $enable
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateAWebhook(string $webhookId, bool $enable = true): array
    {
        $params = ['state' => $enable ? 'active' : 'inactive'];

        return $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $webhookId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->put(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a webhook by ID.
     *
     * @see https://developer.nylas.com/docs/api/v2/#delete-/a/client_id/webhooks/id
     *
     * @param string $webhookId
     *
     * @throws GuzzleException
     */
    public function deleteAWebhook(string $webhookId): void
    {
        $this->options
            ->getSync()
            ->setPath($this->options->getClientId(), $webhookId)
            ->setHeaderParams($this->options->getAuthorizationHeader(false))
            ->delete(API::LIST['oneWebhook']);
    }

    // ------------------------------------------------------------------------------
}
