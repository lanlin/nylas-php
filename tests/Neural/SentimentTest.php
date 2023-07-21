<?php

declare(strict_types = 1);

namespace Tests\Neural;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Message Sending Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class SentimentTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSentimentAnalysisText(): void
    {
        $text = $this->faker->paragraph;

        $this->mockResponse([
            'account_id'      => 'string',
            'model'           => 'sentiment-v0',
            'sentiment'       => 'POSITIVE',
            'sentiment_score' => 0.60000002384186,
            'text'            => 'Hi, thank you so much for reaching out! We can catch up tomorrow.',
        ]);

        $data = $this->client->Neural->Sentiment->sentimentAnalysisText($text);

        static::assertArrayHasKey('text', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSentimentAnalysisMessage(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([[
            'account_id'      => 'string',
            'sentiment'       => 'NEUTRAL',
            'sentiment_score' => 0,
            'text'            => 'Hi, thank you so much for reaching out! We can catch up tomorrow.',
        ]]);

        $data = $this->client->Neural->Sentiment->sentimentAnalysisMessage($id);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSentimentAnalysisFeedback(): void
    {
        $param = [
            'sentiment'  => 'positive',
            'overwrite'  => true,
            'message_id' => 'string',
        ];

        $this->mockResponse([
            'id'            => 'string',
            'status'        => 'string',
            'feedback_at'   => 0,
            'code_version'  => 'string',
            'model_version' => 'string',
        ]);

        $data = $this->client->Neural->Sentiment->sentimentAnalysisFeedback($param);

        static::assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
