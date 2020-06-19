<?php

namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Delta Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class DeltaTest extends Abs
{
    // ------------------------------------------------------------------------------

    public function testGetLatestCursor(): void
    {
        $data = self::$api->Deltas()->Delta()->getLatestCursor();

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

        $data = self::$api->Deltas()->Delta()->getSetOfDeltas($params);

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

        $data = self::$api->Deltas()->Delta()->longPollingDelta($params);

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

        $data = self::$api->Deltas()->Delta()->streamingDelta($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------
}
