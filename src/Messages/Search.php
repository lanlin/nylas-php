<?php

namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message Search
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Search
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Search constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Searches messages.
     *
     * @see https://developer.nylas.com/docs/api/#get/messages/search
     *
     * @param string $keyword
     * @param int    $offset
     * @param int    $limit
     *
     * @return array
     */
    public function searchMessages(string $keyword, int $offset = 0, int $limit = 100): array
    {
        V::doValidate(V::stringType()->notEmpty(), $keyword);

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
