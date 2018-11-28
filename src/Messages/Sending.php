<?php namespace Nylas\Messages;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

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
     * @return mixed
     */
    public function sendDirectly(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $this->getMessageRules()->assert($params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['sending']);
    }

    // ------------------------------------------------------------------------------

    /**
     * send raw mime
     *
     * @TODO boundary
     * @param array $params
     * @return mixed
     */
    public function sendRawMIME(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $html = new MimePart($htmlMarkup);
        $html->type = Mime::TYPE_HTML;
        $html->charset = 'utf-8';
        $html->encoding = Mime::ENCODING_QUOTEDPRINTABLE;

        $image = new MimePart(fopen($pathToImage, 'r'));
        $image->type = 'image/jpeg';
        $image->filename = 'image-file-name.jpg';
        $image->disposition = Mime::DISPOSITION_ATTACHMENT;
        $image->encoding = Mime::ENCODING_BASE64;

        $body = new MimeMessage();
        $body->setParts([$html, $image]);

        $this->getMessageRules()->assert($params);

        $header =
        [
            'Content-Type'  => 'message/rfc822',
            'Authorization' => $params['access_token']
        ];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setBody($body)
        ->setFormParams($params)
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

        $tmp = V::each(V::keySet(
            V::keyOptional('name', V::stringType()),
            V::keyOptional('email', V::stringType())
        ));

        $tracking = V::keySet(
            V::key('links', V::boolType()),
            V::key('opens', V::boolType()),
            V::key('thread_replies', V::boolType()),
            V::keyOptional('payload', V::stringType()->notEmpty())
        );

        return V::keySet(
            V::key('access_token', V::stringType()::notEmpty()),

            V::keyOptional('to', $tmp),
            V::keyOptional('cc', $tmp),
            V::keyOptional('bcc', $tmp),
            V::keyOptional('from', $tmp),
            V::keyOptional('reply_to', $tmp),
            V::keyOptional('reply_to_message_id', V::stringType()::notEmpty()),

            V::keyOptional('body', V::stringType()::notEmpty()),
            V::keyOptional('subject', V::stringType()::notEmpty()),
            V::keyOptional('file_ids', $ids),
            V::keyOptional('tracking', $tracking)
        );
    }

    // ------------------------------------------------------------------------------

}
