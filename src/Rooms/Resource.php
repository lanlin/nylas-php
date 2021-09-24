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
 * @change 2021/09/24
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
     * @return array
     */
    public function returnRoomResourceInformation(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['resource']);
    }

    // ------------------------------------------------------------------------------
}
