<?php

namespace Tests\Messages;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Message Sending Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class SendingTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testSendMessage(): void
    {
        $params =
        [
            'to'      => [['email' => 'test@test.com']],
            'subject' => 'this is for test',
        ];

        $data = $this->client->Messages->Sending->sendDirectly($params);

        $this->assertArrayHasKey('id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testSendRaw(): void
    {
        $content = 'testing send raw';

        $data = $this->client->Messages->Sending->sendRawMIME($content);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------
}
