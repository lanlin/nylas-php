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
        $this->mockResponse([
            [
                'object'       => 'room_resource',
                'email'        => 'training-room-1A@office365.com',
                'name'         => 'Training Room 1A',
                'capacity'     => '8',
                'building'     => 'West Building',
                'floor_name'   => '7',
                'floor_number' => '7',
            ],
        ]);

        $data = $this->client->Rooms->Resource->returnRoomResourceInformation();

        $this->assertArrayHasKey('email', $data[0]);
    }

    // ------------------------------------------------------------------------------
}
