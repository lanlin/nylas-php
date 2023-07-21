<?php

declare(strict_types = 1);

namespace Tests\Rooms;

use JsonException;
use Tests\AbsCase;

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
class ResourceTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
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

        static::assertArrayHasKey('email', $data[0]);
    }

    // ------------------------------------------------------------------------------
}
