<?php

namespace Nylas\Outbox;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Outbox
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Message
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Get a list of all messages scheduled to be sent.
     *
     * @see https://developer.nylas.com/docs/api/#get/outbox
     *
     * @return array
     */
    public function returnAllMessagesToBeSent(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['outbox']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send a message or schedule messages to be sent.
     *
     * @see https://developer.nylas.com/docs/api/#post/outbox
     *
     * @param array $params
     *
     * @return array
     */
    public function sendAMessage(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('to', $this->arrayOfObject()),
            V::keyOptional('from', $this->arrayOfObject()),
            V::keyOptional('body', V::stringType()->notEmpty()),
            V::keyOptional('subject', V::stringType()->notEmpty()),
            V::keyOptional('send_at', V::timestampType()),
        ), $params);

        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['outbox']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Update a message by ID.
     *
     * @see https://developer.nylas.com/docs/api/#put/messages/id
     *
     * @param string $jobStatusId
     * @param int    $sendAt
     *
     * @return array
     */
    public function updateSendTime(string $jobStatusId, ?int $sendAt = null): array
    {
        V::doValidate(V::stringType()->notEmpty(), $jobStatusId);
        V::doValidate(V::anyOf(V::nullType(), V::timestampType()), $sendAt);

        $params = $sendAt ? ['send_at' => $sendAt] : [];

        return $this->options
            ->getSync()
            ->setPath($jobStatusId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneOutbox']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a draft by ID. The draft version must be specified otherwise it will return an error.
     *
     * @see https://developer.nylas.com/docs/api/#delete/drafts/id
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteScheduledMessage(mixed $jobStatusId): array
    {
        $jobStatusId = Helper::fooToArray($jobStatusId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $jobStatusId);

        $queues = [];

        foreach ($jobStatusId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneOutbox']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($jobStatusId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * array of object
     *
     * @return \Nylas\Utilities\Validator
     */
    private function arrayOfObject(): V
    {
        return V::simpleArray(
            V::keySet(
                V::key('email', V::email()),
                V::key('name', V::stringType(), false)
            )
        );
    }

    // ------------------------------------------------------------------------------
}
