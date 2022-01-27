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
 * @change 2022/01/27
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
     * Send an email directly
     *
     * @see https://developer.nylas.com/docs/api/#post/send
     *
     * @param array $params
     *
     * @return array
     */
    public function sendAnEmailDirectly(array $params): array
    {
        $params = Helper::arrayToMulti($params);

        V::doValidate($this->getMessageRules(), $params);

        $queues = [];

        foreach ($params as $item)
        {
            $request = $this->options
                ->getAsync()
                ->setFormParams($item)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->post(API::LIST['sending']);
            };
        }

        return $this->options->getAsync()->pool($queues, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send raw MIME messages
     *
     * Suggest: use zend-mail for raw message
     *
     * @see https://docs.zendframework.com/zend-mail/
     * @see https://developer.nylas.com/docs/api/#post/send
     *
     * @param \Psr\Http\Message\StreamInterface|resource|string $content
     *
     * @return mixed
     */
    public function sendRawMiMeMessage(mixed $content): mixed
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
     * get metadata array rules
     *
     * @see https://developer.nylas.com/docs/developer-tools/api/metadata/#keep-in-mind
     *
     * @return \Nylas\Utilities\Validator
     */
    private function metadataRules(): V
    {
        return V::callback(static function (mixed $input): bool
        {
            if (!\is_array($input) || \count($input) > 50)
            {
                return false;
            }

            $keys = \array_keys($input);
            $isOk = V::each(V::stringType()->length(1, 40))->validate($keys);

            if (!$isOk)
            {
                return false;
            }

            // https://developer.nylas.com/docs/developer-tools/api/metadata/#delete-metadata
            return V::each(V::stringType()->length(0, 500))->validate(\array_values($input));
        });
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
            V::keyOptional('tracking', $tracking),
            V::keyOptional('metadata', $this->metadataRules())
        ));
    }

    // ------------------------------------------------------------------------------
}
