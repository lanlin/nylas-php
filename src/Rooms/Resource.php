<?php

namespace Nylas\Rooms;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Room
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2022/01/27
 */
class Resource
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
     * Which rooms a user can book within their GSuite or Office365 organization
     *
     * @see https://developer.nylas.com/docs/api/#get/resources
     *
     * @param int $limit
     *
     * @return array
     */
    public function returnRoomResourceInformation(int $limit = 100): array
    {
        return $this->options
            ->getSync()
            ->setQuery(['limit' => $limit])
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['resource']);
    }

    // ------------------------------------------------------------------------------
}
