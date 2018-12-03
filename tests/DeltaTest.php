<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Delta Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/29
 */
class DeltaTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetLatestCursor()
    {
        $data = self::$api->Deltas()->Delta()->getLatestCursor();

        $this->assertArrayHasKey('cursor', $data);
    }

    // ------------------------------------------------------------------------------

    public function testGetSetOfDeltas()
    {
        $params =
        [
            'cursor'         => '4whx9f0r544dwd07ipymrj7a1',
            'exclude_types'  => 'message'
        ];

        $data = self::$api->Deltas()->Delta()->getSetOfDeltas($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    public function testLongPollingDelta()
    {
        $params =
        [
            'cursor'         => '4whx9f0r544dwd07ipymrj7a1',
            'timeout'        => 20,
            'exclude_types'  => 'message'
        ];

        $data = self::$api->Deltas()->Delta()->longPollingDelta($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    public function testStreamingDelta()
    {
        $params =
        [
            'cursor'         => '4whx9f0r544dwd07ipymrj7a1',
            'exclude_types'  => 'message'
        ];

        $data = self::$api->Deltas()->Delta()->streamingDelta($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

}
