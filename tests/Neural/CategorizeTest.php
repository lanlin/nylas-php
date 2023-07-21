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
class CategorizeTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testCategorizeAMessage(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([
            [
                'account_id'     => '{account_id}',
                'categorized_at' => 1608244650.0782,
                'category'       => 'feed',
                'id'             => $id,
                'model_version'  => '1734dc47',
            ],
        ]);

        $data = $this->client->Neural->Categorize->categorizeAMessage($id);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testCategorizeMessageFeedback(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([[
            'account_id'         => '{account_id}',
            'category'           => 'conversation',
            'is_primary_label'   => true,
            'message_id'         => '{message_id}',
            'recategorized_at'   => 1615903770.5852,
            'recategorized_from' => [
                'category'      => 'conversation',
                'model_version' => 'string',
            ],
        ]]);

        $data = $this->client->Neural->Categorize->categorizeMessageFeedback($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------
}
