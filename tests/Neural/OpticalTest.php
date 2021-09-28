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
class OpticalTest extends AbsCase
{
    // ------------------------------------------------------------------------------

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

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testOpticalCharacterRecognitionFeedback(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([]);

        $data = $this->client->Neural->Optical->opticalCharacterRecognitionFeedback($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------
}
