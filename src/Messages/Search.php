<?php

declare(strict_types = 1);

namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message Search
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Search
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Search constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Searches messages.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/messages/search
     *
     * @param string $keyword
     * @param int    $offset
     * @param int    $limit
     *
     * @return array
     * @throws GuzzleException
     */
    public function searchMessages(string $keyword, int $offset = 0, int $limit = 100): array
    {
        V::doValidate(V::stringType()::notEmpty(), $keyword);

        $query = [
            'q'      => $keyword,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['searchMessages']);
    }

    // ------------------------------------------------------------------------------
}
