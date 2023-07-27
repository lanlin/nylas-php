<?php

declare(strict_types = 1);

namespace Nylas\Rooms;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Room
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Resource
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Which rooms a user can book within their GSuite or Office365 organization
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/resources
     *
     * @param int $limit
     *
     * @return array
     * @throws GuzzleException
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
