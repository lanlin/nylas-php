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
class Sentiment
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
     * Sentiment analysis will analyze provided text
     *
     * @see https://developer.nylas.com/docs/api/#put/neural/sentiment
     *
     * @param string $text
     *
     * @return array
     */
    public function sentimentAnalysisText(string $text): array
    {
        V::doValidate(V::stringType()->length(1, 1000), $text);

        return $this->options
            ->getSync()
            ->setFormParams(['text' => $text])
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralSment']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Sentiment analysis will analyze provided emails
     *
     * @see https://developer.nylas.com/docs/api/#put/neural/sentiment
     *
     * @param mixed $messageId
     *
     * @return array
     */
    public function sentimentAnalysisMessage(mixed $messageId): array
    {
        $messageId = Helper::fooToArray($messageId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $messageId);

        return $this->options
            ->getSync()
            ->setFormParams(['message_id' => $messageId])
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralSment']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Send feedback about sentiment analysis.
     *
     * @see https://developer.nylas.com/docs/api/#post/neural/sentiment/feedback
     *
     * @param array $params
     *
     * @return array
     */
    public function sentimentAnalysisFeedback(array $params): array
    {
        V::doValidate(V::oneOf(
            V::keySet(
                V::key('text', V::stringType()->notEmpty()),
                V::keyOptional('overwrite', V::boolType()),
                V::keyOptional('sentiment', V::in(['positive', 'negative', 'neutral'])),
            ),
            V::keySet(
                V::key('message_id', V::stringType()->notEmpty()),
                V::keyOptional('overwrite', V::boolType()),
                V::keyOptional('sentiment', V::in(['positive', 'negative', 'neutral'])),
            ),
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralSmentFeedback']);
    }

    // ------------------------------------------------------------------------------
}
