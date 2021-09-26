<?php

namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Threads Search
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
     * Searches threads.
     *
     * @see https://developer.nylas.com/docs/api/#get/threads/search
     *
     * @param string $keyword
     * @param int    $offset
     * @param int    $limit
     * @param string $view  null|expanded
     *
     * @return array
     */
    public function searchThreads(string $keyword, int $offset = 0, int $limit = 100, ?string $view = null): array
    {
        V::doValidate(V::in([null, 'expanded']), $view);
        V::doValidate(V::stringType()->notEmpty(), $keyword);

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
