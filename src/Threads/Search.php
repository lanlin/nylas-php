<?php namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Threads Search
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Search
{

    // ------------------------------------------------------------------------------

    /**
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    // ------------------------------------------------------------------------------

    /**
     * search threads list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function threads(array $params)
    {
        $rules = V::keySet(
            V::key('q', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $query  = ['q' => $params['q']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['searchThreads']);
    }

    // ------------------------------------------------------------------------------

}
