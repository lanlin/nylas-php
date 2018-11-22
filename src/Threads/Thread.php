<?php namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Thread
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
 */
class Thread
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
     * get accounts list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getThreadsList(array $params)
    {
        if (!$this->getThreadsRules()->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $query =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);
        $query = array_merge($params, $query);

        return $this->request->setQuery($query)->setHeaderParams($header)->get(API::LIST['threads']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get thread info
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getThread(array $params)
    {
        $rules = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request->setPath($path)->setHeaderParams($header)->get(API::LIST['oneThread']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get accounts list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addThread(array $params)
    {
        $rules = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),

            V::key('unread', V::boolType(), false),
            V::key('starred', V::boolType(), false),
            V::key('folder_id', V::stringType()::notEmpty(), false),
            V::key('label_ids', V::arrayVal()->each(V::stringType(), V::intType()), false)
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token'], $params['id']);

        return $this->request
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneThread']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get threads list filter rules
     *
     * @link https://docs.nylas.com/reference#get-threads
     * @return \Respect\Validation\Validator
     */
    private function getThreadsRules()
    {
        return V::keySet(
            V::key('in', V::stringType()::notEmpty(), false),
            V::key('to', V::email(), false),
            V::key('from', V::email(), false),
            V::key('cc', V::email(), false),
            V::key('bcc', V::email(), false),
            V::key('subject', V::stringType()::notEmpty(), false),
            V::key('any_email', V::stringType()::notEmpty(), false),

            V::key('started_after', V::timestampType(), false),
            V::key('started_before', V::timestampType(), false),
            V::key('last_message_after', V::timestampType(), false),
            V::key('last_message_before', V::timestampType(), false),

            V::key('limit', V::intType()::min(1), false),
            V::key('offset', V::intType()::min(0), false),
            V::key('view', V::in(['ids', 'count', 'expanded']), false),
            V::key('unread', V::boolType(), false),
            V::key('starred', V::boolType(), false),
            V::key('filename', V::stringType()::notEmpty(), false),

            V::key('access_token', V::stringType()::notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

}
