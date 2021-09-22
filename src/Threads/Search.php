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
 * @change 2020/04/26
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
     * search threads list
     *
     * @param string $q
     *
     * @return array
     */
    public function threads(string $q): array
    {
        V::doValidate(V::stringType()->notEmpty(), $q);

        return $this->options
            ->getSync()
            ->setQuery(['q' => $q])
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['searchThreads']);
    }

    // ------------------------------------------------------------------------------
}
