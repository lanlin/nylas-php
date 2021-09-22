<?php

namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Thread
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Thread
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Thread constructor.
     *
     * @param \Nylas\Utilities\Options $options
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
     */
    public function returnsAllThreads(array $params = []): array
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->getThreadsRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $query = [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        $query  = \array_merge($params, $query);
        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($header)
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
        $threadId    = Helper::fooToArray($threadId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $threadId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneThread'];
        $header = ['Authorization' => $accessToken];

        foreach ($threadId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->get($target);
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
     */
    public function updateAThread(string $threadId, array $params): array
    {
        $accessToken = $this->options->getAccessToken();

        $rules = V::keySet(
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('folder_id', V::stringType()->notEmpty()),
            V::keyOptional('label_ids', V::simpleArray(V::stringType()))
        );

        V::doValidate($rules, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
            ->getSync()
            ->setPath($threadId)
            ->setFormParams($params)
            ->setHeaderParams($header)
            ->put(API::LIST['oneThread']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get threads list filter rules
     *
     * @see https://docs.nylas.com/reference#get-threads
     *
     * @return \Nylas\Utilities\Validator
     */
    private function getThreadsRules(): V
    {
        return V::keySet(
            V::keyOptional('to', V::email()),
            V::keyOptional('cc', V::email()),
            V::keyOptional('bcc', V::email()),
            V::keyOptional('from', V::email()),
            V::keyOptional('in', V::stringType()->notEmpty()),
            V::keyOptional('not_in', V::stringType()->notEmpty()),
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('subject', V::stringType()->notEmpty()),
            V::keyOptional('filename', V::stringType()->notEmpty()),
            V::keyOptional('any_email', V::stringType()->notEmpty()),
            V::keyOptional('started_after', V::timestampType()),
            V::keyOptional('started_before', V::timestampType()),
            V::keyOptional('last_message_after', V::timestampType()),
            V::keyOptional('last_message_before', V::timestampType()),
        );
    }

    // ------------------------------------------------------------------------------
}
