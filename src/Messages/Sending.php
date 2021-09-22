<?php

namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message Sending
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class Sending
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Sending constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * send message directly
     *
     * @param array $params
     *
     * @return array
     */
    public function sendDirectly(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate($this->getMessageRules(), $params);

        $queues = [];
        $target = API::LIST['sending'];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setFormParams($item)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request, $target)
            {
                return $request->post($target);
            };
        }

        return $this->options->getAsync()->pool($queues, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * send raw mime
     *
     * Suggest: use zend-mail for raw message
     *
     * @see https://docs.zendframework.com/zend-mail/
     *
     * @param \Psr\Http\Message\StreamInterface|resource|string $content
     *
     * @return mixed
     */
    public function sendRawMIME(mixed $content)
    {
        V::doValidate(V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        ), $content);

        $header = $this->options->getAuthorizationHeader();

        $header['Content-Type'] = 'message/rfc822';

        return $this->options
            ->getSync()
            ->setBody($content)
            ->setHeaderParams($header)
            ->post(API::LIST['sending']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get message sending rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function getMessageRules(): V
    {
        $ids = V::simpleArray(V::stringType()->notEmpty());

        $tmp = V::simpleArray(V::keySet(
            V::key('name', V::stringType(), false),
            V::key('email', V::email())
        ));

        $tracking = V::keySet(
            V::key('links', V::boolType()),
            V::key('opens', V::boolType()),
            V::key('thread_replies', V::boolType()),
            V::keyOptional('payload', V::stringType()->notEmpty())
        );

        return V::simpleArray(V::keySet(
            V::keyOptional('to', $tmp),
            V::keyOptional('cc', $tmp),
            V::keyOptional('bcc', $tmp),
            V::keyOptional('from', $tmp),
            V::keyOptional('reply_to', $tmp),
            V::keyOptional('reply_to_message_id', V::stringType()->notEmpty()),
            V::keyOptional('body', V::stringType()->notEmpty()),
            V::keyOptional('subject', V::stringType()->notEmpty()),
            V::keyOptional('file_ids', $ids),
            V::keyOptional('tracking', $tracking)
        ));
    }

    // ------------------------------------------------------------------------------
}
