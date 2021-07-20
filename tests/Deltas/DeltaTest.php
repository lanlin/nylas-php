<?php

namespace Tests\Deltas;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Delta Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class DeltaTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetLatestCursor(): void
    {
        $data = $this->client->Deltas()->Delta()->getLatestCursor();

        $this->assertArrayHasKey('cursor', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetSetOfDeltas(): void
    {
        $params =
        [
            'cursor'         => '4whx9f0r544dwd07ipymrj7a1',
            'exclude_types'  => 'message',
        ];

        $data = $this->client->Deltas()->Delta()->getSetOfDeltas($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    public function testLongPollingDelta(): void
    {
        $params =
        [
            'cursor'         => '4whx9f0r544dwd07ipymrj7a1',
            'timeout'        => 20,
            'exclude_types'  => 'message',
        ];

        $data = $this->client->Deltas()->Delta()->longPollingDelta($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    public function testStreamingDelta(): void
    {
        $params =
        [
            'cursor'         => '4whx9f0r544dwd07ipymrj7a1',
            'exclude_types'  => 'message',
        ];

        $data = $this->client->Deltas()->Delta()->streamingDelta($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------
}
