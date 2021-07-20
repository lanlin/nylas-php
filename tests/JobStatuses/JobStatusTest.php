<?php

namespace Tests\JobStatuses;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * JobStatus Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/05/05
 *
 * @internal
 */
class JobStatusTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetJobStatusList(): void
    {
        $data = $this->client->JobStatuses()->JobStatus()->getJobStatusesList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetJobStatus(): void
    {
        $params = ['job_status_id' => 'csihlkp7geos1org29z02xzm8'];

        $data = $this->client->JobStatuses()->JobStatus()->getJobStatus($params);

        //@NOTE: Format is [ '<job_status_id>' => [data in k => v]]
        $this->assertArrayHasKey($params['job_status_id'], $data);
        $this->assertArrayHasKey('job_status_id', $data[$params['job_status_id']]);
    }

    // ------------------------------------------------------------------------------
}
