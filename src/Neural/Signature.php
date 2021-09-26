<?php

namespace Nylas\Neural;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Neural
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/24
 */
class Signature
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
     * The Signature endpoint will extract email signatures by removing extra images and HTML.
     *
     * @see https://developer.nylas.com/docs/api/#put/neural/signature
     *
     * @param mixed $messageId
     * @param array $params
     *
     * @return array
     */
    public function signatureExtraction(mixed $messageId, array $params = []): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $messageId);

        V::doValidate(V::keySet(
            V::keyOptional('ignore_links', V::boolType()),
            V::keyOptional('ignore_images', V::boolType()),
            V::keyOptional('ignore_tables', V::boolType()),
            V::keyOptional('parse_contacts', V::boolType()),
            V::keyOptional('images_as_markdown', V::boolType()),
            V::keyOptional('remove_conclusion_phrases', V::boolType()),
        ), $params);

        $params['message_id'] = $messageId;

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralSign']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send feedback about signature extraction.
     *
     * @see https://developer.nylas.com/docs/api/#post/neural/signature/feedback
     *
     * @param mixed $messageId
     *
     * @return array
     */
    public function signatureExtractionFeedback(mixed $messageId): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $messageId);

        $queues = [];

        foreach ($messageId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setFormParams(['message_id' => $id])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->post(API::LIST['neuralSignFeedback']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($messageId, $pools);
    }

    // ------------------------------------------------------------------------------
}
