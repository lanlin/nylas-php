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
class OpticalTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSendMessage(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([
            'account_id'   => '{account_id}',
            'content_id'   => '<f_kiubrkoa0>',
            'content_type' => 'application/pdf',
            'filename'     => 'intelligent-workflow-automations-coming-to-the-nylas-platform.pdf',
            'id'           => '{id}',
            'message_ids'  => ['{message_ids}'],
            'object'       => 'file',
            'ocr'          => ['faslfjalsdflasld'],
            'size'         => 0,
        ]);

        $data = $this->client->Neural->Optical->opticalCharacterRecognition($id);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testOpticalCharacterRecognitionFeedback(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([]);

        $data = $this->client->Neural->Optical->opticalCharacterRecognitionFeedback($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------
}
