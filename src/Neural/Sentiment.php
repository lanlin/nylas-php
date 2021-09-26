<?php

namespace Nylas\Neural;

use Nylas\Utilities\API;
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
     * Sentiment analysis will analyze provided text or emails and give you an emotional opinion on the text.
     *
     * @see https://developer.nylas.com/docs/api/#put/neural/sentiment
     *
     * @param string $string
     * @param string $type  text|message_id
     *
     * @return array
     */
    public function sentimentAnalysis(string $string, string $type = 'message_id'): array
    {
        V::doValidate(V::in(['text', 'message_id']), $type);
        V::doValidate(V::stringType()->length(1, 1000), $string);

        $params = match ($type)
        {
            'text'       => $string,
            'message_id' => ['message_id' => [$string]],
        };

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['neuralOcr']);
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
            ->put(API::LIST['neuralOcrFeedback']);
    }

    // ------------------------------------------------------------------------------
}
