<?php

declare(strict_types = 1);

namespace Nylas\Neural;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Neural
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 */
class Conversation
{
    // ------------------------------------------------------------------------------

    /**
     * @var Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Message constructor.
     *
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * Parse email messages by removing extra HTML.
     *
     * @see https://developer.nylas.com/docs/api/v2/#put-/neural/conversation
     *
     * @param string $messageId
     * @param array  $params
     *
     * @return array
     * @throws GuzzleException
     */
    public function cleanConversation(string $messageId, array $params = []): array
    {
        $params['message_id'] = [$messageId];

        V::doValidate(V::keySet(
            V::key('message_id', V::simpleArray(V::stringType()::notEmpty())),
            V::keyOptional('ignore_links', V::boolType()),
            V::keyOptional('ignore_images', V::boolType()),
            V::keyOptional('ignore_tables', V::boolType()),
            V::keyOptional('images_as_markdown', V::boolType()),
            V::keyOptional('remove_conclusion_phrases', V::boolType()),
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralConv']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send feedback on the conversation endpoint.
     *
     * @see https://developer.nylas.com/docs/api/v2/#post-/neural/conversation/feedback
     *
     * @param mixed $messageId
     *
     * @return array
     */
    public function cleanConversationsFeedback(mixed $messageId): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $messageId);

        $queues = [];

        foreach ($messageId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setFormParams(['message_id' => $id])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->post(API::LIST['neuralConvFeedback']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($messageId, $pools);
    }

    // ------------------------------------------------------------------------------
}
