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
class Categorize
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
     * Returns the message category as part of the Message object. Messages are either conversation or feed.
     *
     * @see https://developer.nylas.com/docs/api/#put/neural/categorize
     *
     * @param mixed $messageId
     * @param bool  $onlyCategory
     *
     * @return array
     * @throws GuzzleException
     */
    public function categorizeAMessage(mixed $messageId, bool $onlyCategory = false): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::arrayType()::length(1, 5), $messageId);
        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $messageId);

        return $this->options
            ->getSync()
            ->setFormParams(['messageId' => $messageId, 'only_category' => $onlyCategory])
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralCate']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send feedback on categorized messages.
     *
     * @see https://developer.nylas.com/docs/api/#post/neural/categorize/feedback
     *
     * @param mixed  $messageId
     * @param string $category
     *
     * @return array
     */
    public function categorizeMessageFeedback(mixed $messageId, string $category = 'feed'): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::in(['feed', 'conversation']), $category);
        V::doValidate(V::simpleArray(V::stringType()::notEmpty()), $messageId);

        $queues = [];

        foreach ($messageId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setFormParams(['message_id' => $id, 'category' => $category])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->post(API::LIST['neuralCateFeedback']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($messageId, $pools);
    }

    // ------------------------------------------------------------------------------
}
