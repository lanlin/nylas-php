<?php namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;
use ZBateson\MailMimeParser\MailMimeParser;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
 */
class Message
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
     * get messages list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getMessagesList(array $params)
    {
        if (!$this->getMessagesRules()->validate($params))
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

        return $this->request->setQuery($query)->setHeaderParams($header)->get(API::LIST['messages']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get message info
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getMessage(array $params)
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

        return $this->request->setPath($path)->setHeaderParams($header)->get(API::LIST['oneMessage']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get raw message info
     *
     * @param array $params
     * @return \ZBateson\MailMimeParser\Message
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getRawMessage(array $params)
    {
        $rules = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path = [$params['id']];

        $header =
        [
            'Accept'        => 'message/rfc822',        // RFC-2822 message object
            'Authorization' => $params['access_token']
        ];

        $rawStream = $this->request->setPath($path)->setHeaderParams($header)->get(API::LIST['oneMessage']);

        // parse mime data
        return (new MailMimeParser())->parse($rawStream);
    }

    // ------------------------------------------------------------------------------

    /**
     * get accounts list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addMessage(array $params)
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
        ->put(API::LIST['oneMessage']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get messages list filter rules
     *
     * @link https://docs.nylas.com/reference#messages-1
     * @return \Respect\Validation\Validator
     */
    private function getMessagesRules()
    {
        return V::keySet(
            V::key('in', V::stringType()::notEmpty(), false),
            V::key('to', V::email(), false),
            V::key('from', V::email(), false),
            V::key('cc', V::email(), false),
            V::key('bcc', V::email(), false),
            V::key('subject', V::stringType()::notEmpty(), false),
            V::key('any_email', V::stringType()::notEmpty(), false),
            V::key('thread_id', V::stringType()::notEmpty(), false),

            V::key('received_after', V::timestampType(), false),
            V::key('received_before', V::timestampType(), false),
            V::key('has_attachment', V::boolType(), false),

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
