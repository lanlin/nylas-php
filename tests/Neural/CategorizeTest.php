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
class CategorizeTest extends AbsCase
{
    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------
}
