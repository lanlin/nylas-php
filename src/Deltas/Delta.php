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
 * @change 2020/09/30
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
     * get latest cursor
     *
     * @return array
     */
    public function getLatestCursor(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['deltaLatestCursor']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get a set of deltas
     *
     * @param array $params
     *
     * @return array
     */
    public function getSetOfDeltas(array $params): array
    {
        V::doValidate(V::keySet(...$this->getBaseRules()), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['delta']);
    }

    // ------------------------------------------------------------------------------

    /**
     * long polling delta updates
     *
     * @param array $params
     *
     * @return array
     */
    public function longPollingDelta(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('timeout', V::intType()->min(1)),
            ...$this->getBaseRules(),
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['deltaLongpoll']);
    }

    // ------------------------------------------------------------------------------

    /**
     * streaming delta updates
     *
     * @param array $params
     *
     * @return mixed
     */
    public function streamingDelta(array $params)
    {
        V::doValidate(V::keySet(...$this->getBaseRules()), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['deltaStreaming']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return array
     */
    private function getBaseRules(): array
    {
        $types = ['contact', 'event', 'file', 'message', 'draft', 'thread', 'folder', 'label'];

        return
        [
            V::key('cursor', V::stringType()->notEmpty()),

            V::keyOptional('view', V::equals('expanded')),
            V::keyOptional('exclude_types', V::in($types)),
            V::keyOptional('include_types', V::in($types)),
        ];
    }

    // ------------------------------------------------------------------------------
}
