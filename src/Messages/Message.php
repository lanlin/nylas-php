<?php namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
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
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
    public function getMessagesList(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate($this->getMessagesRules(), $params);

        $query =
        [
            'limit'  => $params['limit'] ?? 100,
            'offset' => $params['offset'] ?? 0,
        ];

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);
        $query = array_merge($params, $query);

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['messages']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get message info
     *
     * @param string $messageId
     * @param string $accessToken
     * @return array
     */
    public function getMessage(string $messageId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $messageId,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rules = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneMessage']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get raw message info
     *
     * @param string $messageId
     * @param string $accessToken
     * @return \ZBateson\MailMimeParser\Message
     */
    public function getRawMessage(string $messageId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $messageId,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rules = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rules, $params);

        $header =
        [
            'Accept'        => 'message/rfc822',        // RFC-2822 message object
            'Authorization' => $params['access_token']
        ];

        $rawStream = $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneMessage']);

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
    public function updateMessage(array $params)
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
        ->getRequest()
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
            V::keyOptional('filename', V::stringType()->notEmpty()),

            V::key('access_token', V::stringType()->notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

}
