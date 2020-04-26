<?php namespace Nylas\Threads;

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
     * @return array
     */
    public function threads(string $q) : array
    {
        $params =
        [
            'q'            => $q,
            'access_token' => $this->options->getAccessToken(),
        ];

        $rules = V::keySet(
            V::key('q', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $query  = ['q' => $params['q']];
        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getSync()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['searchThreads']);
    }

    // ------------------------------------------------------------------------------

}
