<?php

namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use ZBateson\MailMimeParser\Message as MSG;
use ZBateson\MailMimeParser\MailMimeParser;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message
 * ----------------------------------------------------------------------------------
 *
 * @info include inline image <img src="cid:file_id">
 *
 * @author lanlin
 * @change 2021/09/22
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
     * Returns all messages. Use the query parameters to filter the data.
     *
     * @param array $params
     *
     * @return array
     */
    public function returnAllMessages(array $params = []): array
    {
        V::doValidate($this->getMessagesRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['messages']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a message by ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/messages/id
     *
     * @param mixed $messageId string|string[]
     * @param bool  $expanded  true|false
     *
     * @return array
     */
    public function returnAMessage(mixed $messageId, bool $expanded = false): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $messageId);

        $messageId = \implode(',', $messageId);
        $queryPara = $expanded ? ['view' => 'expanded'] : [];

        return $this->options
            ->getSync()
            ->setPath($messageId)
            ->setQuery($queryPara)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['oneMessage']);
    }

    // ------------------------------------------------------------------------------

    /**
     * This will return the message in RFC 2822 format, including all MIME body subtypes and attachments.
     *
     * @see https://developer.nylas.com/docs/api/#get/messages/id
     *
     * @param string $messageId
     *
     * @return MSG
     */
    public function returnARawMessage(string $messageId): MSG
    {
        $header = $this->options->getAuthorizationHeader();

        V::doValidate(V::stringType()->notEmpty(), $messageId);

        $header['Accept'] = 'message/rfc822'; // RFC-2822 message object

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
     * Update a message by ID.
     *
     * @see https://developer.nylas.com/docs/api/#put/messages/id
     *
     * @param string $messageId
     * @param array  $params
     *
     * @return array
     */
    public function updateAMessage(string $messageId, array $params): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('unread', V::boolType()),
            V::keyOptional('starred', V::boolType()),
            V::keyOptional('folder_id', V::stringType()->notEmpty()),
            V::keyOptional('label_ids', V::simpleArray(V::stringType()))
        ), $params);

        return $this->options
            ->getSync()
            ->setPath($messageId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneMessage']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get messages list filter rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function getMessagesRules(): V
    {
        return V::keySet(
            V::keyOptional('in', V::stringType()->notEmpty()),
            V::keyOptional('to', V::email()),
            V::keyOptional('cc', V::email()),
            V::keyOptional('bcc', V::email()),
            V::keyOptional('from', V::email()),
            V::keyOptional('subject', V::stringType()->notEmpty()),
            V::keyOptional('any_email', V::stringType()->notEmpty()),
            V::keyOptional('thread_id', V::stringType()->notEmpty()),
            V::keyOptional('received_after', V::timestampType()),
            V::keyOptional('received_before', V::timestampType()),
            V::keyOptional('has_attachment', V::equals(true)),
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
