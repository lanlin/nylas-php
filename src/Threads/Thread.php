<?php

declare(strict_types = 1);

namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Thread
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Thread
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Thread constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns one or more threads that match the filter specified by the query parameters
     *
     * @see https://developer.nylas.com/docs/api/#get/threads
     *
     * @param array $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function returnsAllThreads(array $params = []): array
    {
        V::doValidate($this->getThreadsRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['threads']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns the thread by the specified thread ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/threads/id
     *
     * @param mixed $threadId string|string[]
     *
     * @return array
     */
    public function returnsAThread(mixed $threadId): array
    {
        $threadId = Helper::fooToArray($threadId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $threadId);

        $queues = [];

        foreach ($threadId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneThread']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($threadId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * When you update a thread, all message in a thread are updated.
     *
     * @see https://developer.nylas.com/docs/api/#put/threads/id
     *
     * @param string $threadId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function updateAThread(string $threadId, array $params): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('folder_id', V::stringType()::notEmpty()),
            V::keyOptional('label_ids', V::simpleArray(V::stringType()))
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($threadId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneThread']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get threads list filter rules
     *
     * @see https://docs.nylas.com/reference#get-threads
     *
     * @return V
     */
    private function getThreadsRules(): V
    {
        return V::keySet(
            V::keyOptional('to', V::email()),
            V::keyOptional('cc', V::email()),
            V::keyOptional('bcc', V::email()),
            V::keyOptional('from', V::email()),
            V::keyOptional('in', V::stringType()::notEmpty()),
            V::keyOptional('not_in', V::stringType()::notEmpty()),
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('limit', V::intType()::min(1)),
            V::keyOptional('offset', V::intType()::min(0)),
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('subject', V::stringType()::notEmpty()),
            V::keyOptional('filename', V::stringType()::notEmpty()),
            V::keyOptional('any_email', V::stringType()::notEmpty()),
            V::keyOptional('started_after', V::timestampType()),
            V::keyOptional('started_before', V::timestampType()),
            V::keyOptional('last_message_after', V::timestampType()),
            V::keyOptional('last_message_before', V::timestampType()),
        );
    }

    // ------------------------------------------------------------------------------
}
