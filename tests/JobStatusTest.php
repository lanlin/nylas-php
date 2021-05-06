<?php

namespace Nylas\Tests;

/**
 * ----------------------------------------------------------------------------------
 * JobStatus Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2021/05/05
 *
 * @internal
 */
class JobStatusTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetJobStatusList(): void
    {
        $data = $this->client->JobStatuses()->JobStatus()->getJobStatusList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetJobStatus(): void
    {
        $params = ['job_status_id' => '2k6yf5y2660mqjzwbg3aig92i'];

        $data = $this->client->JobStatuses()->JobStatus()->getJobStatus($params);

        $this->assertArrayHasKey('job_status_id', $data);
    }

    // ------------------------------------------------------------------------------
}
