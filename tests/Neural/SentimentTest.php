<?php

namespace Tests\Neural;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Message Sending Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/28
 *
 * @internal
 */
class SentimentTest extends AbsCase
{
    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey('text', $data);
    }

    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------
}
