<?php

namespace Nylas\Deltas;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Deltas
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Delta
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Delta constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Return a delta cursor. The delta cursor is used to return data using the other deltas endpoints.
     *
     * @see https://developer.nylas.com/docs/api/#post/delta/latest_cursor
     *
     * @return array
     */
    public function getADeltaCursor(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['deltaLatestCursor']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a set of delta cursors.
     *
     * @see https://developer.nylas.com/docs/api/#get/delta
     *
     * @param array $params
     *
     * @return array
     */
    public function requestDeltaCursors(array $params): array
    {
        V::doValidate($this->getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($this->parseTypesToString($params))
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['delta']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Long Polling deltas will instruct the server to keep the connection open until a change comes through or it times out.
     *
     * @see https://developer.nylas.com/docs/api/#get/delta/longpoll
     *
     * @param array $params
     *
     * @return array
     */
    public function returnLongPollingDeltas(array $params): array
    {
        V::doValidate($this->getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($this->parseTypesToString($params))
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['deltaLongpoll']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Streaming deltas process real-time updates.
     *
     * @see https://developer.nylas.com/docs/api/#get/delta/streaming
     *
     * @param array $params
     *
     * @return mixed
     */
    public function streamingDeltas(array $params)
    {
        V::doValidate($this->getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($this->parseTypesToString($params))
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['deltaStreaming']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return V
     */
    private function getBaseRules(): V
    {
        $types = ['contact', 'event', 'file', 'message', 'draft', 'thread', 'folder', 'label'];

        return V::keySet(
            V::key('cursor', V::stringType()->notEmpty()),
            V::keyOptional('view', V::equals('expanded')),
            V::keyOptional('exclude_types', V::simpleArray(V::in($types))),
            V::keyOptional('include_types', V::simpleArray(V::in($types))),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $params
     *
     * @return array
     */
    private function parseTypesToString(array $params): array
    {
        if (!empty($params['exclude_types']))
        {
            $params['exclude_types'] = \implode(',', $params['exclude_types']);
        }

        if (!empty($params['include_types']))
        {
            $params['include_types'] = \implode(',', $params['include_types']);
        }

        return $params;
    }

    // ------------------------------------------------------------------------------
}
