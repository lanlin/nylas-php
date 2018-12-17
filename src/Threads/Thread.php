<?php namespace Nylas\Threads;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Thread
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
 */
class Thread
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Thread constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get threads list
     *
     * @param array $params
     * @return array
     */
    public function getThreadsList(array $params = [])
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate($this->getThreadsRules(), $params);

        $query =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);
        $query = array_merge($params, $query);

        return $this->options
        ->getSync()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['threads']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update thread
     *
     * @param array $params
     * @return array
     */
    public function updateThread(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rules = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),

            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('folder_id', V::stringType()->notEmpty()),
            V::keyOptional('label_ids', V::arrayVal()->each(V::stringType(), V::intType()))
        );

        V::doValidate($rules, $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token'], $params['id']);

        return $this->options
        ->getSync()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneThread']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get thread info
     *
     * @param string $threadId
     * @param string $accessToken
     * @return array
     */
    public function getThread(string $threadId, string $accessToken = null)
    {
        $threadId    = Helper::fooToArray($threadId);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $threadId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneThread'];
        $header = ['Authorization' => $accessToken];

        foreach ($threadId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($threadId, $pools);
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
            V::keyOptional('in', V::stringType()->notEmpty()),
            V::keyOptional('to', V::email()),
            V::keyOptional('from', V::email()),
            V::keyOptional('cc', V::email()),
            V::keyOptional('bcc', V::email()),
            V::keyOptional('subject', V::stringType()->notEmpty()),
            V::keyOptional('any_email', V::stringType()->notEmpty()),

            V::keyOptional('started_after', V::timestampType()),
            V::keyOptional('started_before', V::timestampType()),
            V::keyOptional('last_message_after', V::timestampType()),
            V::keyOptional('last_message_before', V::timestampType()),

            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('filename', V::stringType()->notEmpty()),

            V::key('access_token', V::stringType()->notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

}
