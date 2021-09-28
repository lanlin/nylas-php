<?php

namespace Tests\Rooms;

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
class ResourceTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllMessagesToBeSent(): void
    {
        $this->mockResponse([]);

        $data = $this->client->Rooms->Resource->returnRoomResourceInformation();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------
}
