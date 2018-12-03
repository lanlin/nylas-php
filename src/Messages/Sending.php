<?php namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Message Sending
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/26
 */
class Sending
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
     * @return array
     */
    public function sendDirectly(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate($this->getMessageRules(), $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getSync()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['sending']);
    }

    // ------------------------------------------------------------------------------

    /**
     * send raw mime
     *
     * Suggest: use zend-mail for raw message
     *
     * @link https://docs.zendframework.com/zend-mail/
     * @param string|resource|\Psr\Http\Message\StreamInterface $content
     * @param string $accessToken
     * @return mixed
     */
    public function sendRawMIME($content, string $accessToken = null)
    {
        $rule = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate($rule, $content);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header =
        [
            'Content-Type'  => 'message/rfc822',
            'Authorization' => $accessToken
        ];

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
     * @return \Respect\Validation\Validator
     */
    private function getMessageRules()
    {
        $ids = V::arrayVal()->each(V::stringType()->notEmpty(), V::intType());

        $tmp = V::arrayType()->each(V::keySet(
            V::key('name', V::stringType(), false),
            V::key('email', V::email())
        ));

        $tracking = V::keySet(
            V::key('links', V::boolType()),
            V::key('opens', V::boolType()),
            V::key('thread_replies', V::boolType()),
            V::keyOptional('payload', V::stringType()->notEmpty())
        );

        return V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),

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
        );
    }

    // ------------------------------------------------------------------------------

}
