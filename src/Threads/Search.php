<?php

declare(strict_types = 1);

namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Threads Search
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
     * Searches threads.
     *
     * @see https://developer.nylas.com/docs/api/v2/#get-/threads/search
     *
     * @param string      $keyword
     * @param int         $offset
     * @param int         $limit
     * @param null|string $view    null|expanded
     *
     * @return array
     * @throws GuzzleException
     */
    public function searchThreads(string $keyword, int $offset = 0, int $limit = 100, ?string $view = null): array
    {
        V::doValidate(V::in([null, 'expanded']), $view);
        V::doValidate(V::stringType()::notEmpty(), $keyword);

        $query = [
            'q'      => $keyword,
            'limit'  => $limit,
            'offset' => $offset,
        ];

        if (!empty($view))
        {
            $query['view'] = $view;
        }

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['searchThreads']);
    }

    // ------------------------------------------------------------------------------
}
