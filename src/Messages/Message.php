<?php namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use ZBateson\MailMimeParser\MailMimeParser;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message
 * ----------------------------------------------------------------------------------
 *
 * @info include inline image <img src="cid:file_id">
 * @author lanlin
 * @change 2020/04/26
 */
class Message
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get messages list
     *
     * @param array $params
     * @return array
     */
    public function getMessagesList(array $params = []) : array
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->getMessagesRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $query =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        $query  = array_merge($params, $query);
        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['messages']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get raw message info
     *
     * @param string $messageId
     * @return \ZBateson\MailMimeParser\Message
     */
    public function getRawMessage(string $messageId) : \ZBateson\MailMimeParser\Message
    {
        $rule = V::stringType()->notEmpty();

        $accessToken = $this->options->getAccessToken();

        V::doValidate($rule, $messageId);
        V::doValidate($rule, $accessToken);

        $header =
        [
            'Accept'        => 'message/rfc822',        // RFC-2822 message object
            'Authorization' => $accessToken
        ];

        $rawStream = $this->options
        ->getSync()
        ->setPath($messageId)
        ->setHeaderParams($header)
        ->getStream(API::LIST['oneMessage']);

        // parse mime data
        // @link https://github.com/zbateson/mail-mime-parser
        return (new MailMimeParser())->parse($rawStream);
    }

    // ------------------------------------------------------------------------------

    /**
     * update message status & flags
     *
     * @param array $params
     * @return array
     */
    public function updateMessage(array $params) : array
    {
        $accessToken = $this->options->getAccessToken();

        $rules = V::keySet(
            V::key('id', V::stringType()->notEmpty()),

            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('folder_id', V::stringType()->notEmpty()),
            V::keyOptional('label_ids', V::arrayVal()->each(V::stringType()))
        );

        V::doValidate($rules, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $path   = $params['id'];
        $header = ['Authorization' => $accessToken];

        unset($params['id']);

        return $this->options
        ->getSync()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneMessage']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get message info
     *
     * @param string|array $messageId
     * @return array
     */
    public function getMessage($messageId) : array
    {
        $messageId   = Helper::fooToArray($messageId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $messageId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneMessage'];
        $header = ['Authorization' => $accessToken];

        foreach ($messageId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($messageId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * get messages list filter rules
     *
     * @link https://docs.nylas.com/reference#messages-1
     * @return \Respect\Validation\Validator
     */
    private function getMessagesRules() : \Respect\Validation\Validator
    {
        return V::keySet(
            V::keyOptional('in', V::stringType()->notEmpty()),
            V::keyOptional('to', V::email()),
            V::keyOptional('from', V::email()),
            V::keyOptional('cc', V::email()),
            V::keyOptional('bcc', V::email()),
            V::keyOptional('subject', V::stringType()->notEmpty()),
            V::keyOptional('any_email', V::stringType()->notEmpty()),
            V::keyOptional('thread_id', V::stringType()->notEmpty()),

            V::keyOptional('received_after', V::timestampType()),
            V::keyOptional('received_before', V::timestampType()),
            V::keyOptional('has_attachment', V::boolType()),

            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('view', V::in(['ids', 'count', 'expanded'])),
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('filename', V::stringType()->notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

}
