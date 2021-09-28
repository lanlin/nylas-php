<?php

namespace Tests\JobStatuses;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * JobStatus Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class JobStatusTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllJobStatuses(): void
    {
        $this->mockResponse([
            [
                'account_id'    => 'eof2wrhqkl7kdwhy9hylpv9o9',
                'action'        => 'create_calendar',
                'created_at'    => 1592374298,
                'id'            => '8e570s302fdazx9zqwiuk9jqn',
                'job_status_id' => '48pp6ijzrxpw9jors9ylnsxnf',
                'object'        => 'calendar',
                'status'        => 'successful',
            ],
            [
                'account_id'    => 'eof2wrhqkl7kdwhy9hylpv9o9',
                'action'        => 'update_calendar',
                'created_at'    => 1592375249,
                'id'            => '8e570s302fdazx9zqwiuk9jqn',
                'job_status_id' => 'aqghhhldmq8eyxnn14z0tlsun',
                'object'        => 'calendar',
                'status'        => 'successful',
            ],
            [
                'account_id'    => 'eof2wrhqkl7kdwhy9hylpv9o9',
                'action'        => 'delete_calendar',
                'created_at'    => 1592375759,
                'id'            => '8e570s302fdazx9zqwiuk9jqn',
                'job_status_id' => 'd38mgop88je0agkqrf03sw0sw',
                'object'        => 'calendar',
                'status'        => 'successful',
            ],
        ]);

        $data = $this->client->JobStatuses->JobStatus->returnAllJobStatuses();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testGetJobStatus(): void
    {
        $id = '8e570s302fdazx9zqwiuk9jqn';

        $this->mockResponse([
            [
                'account_id'    => 'eof2wrhqkl7kdwhy9hylpv9o9',
                'action'        => 'create_calendar',
                'created_at'    => 1592374298,
                'id'            => '8e570s302fdazx9zqwiuk9jqn',
                'job_status_id' => '48pp6ijzrxpw9jors9ylnsxnf',
                'object'        => 'calendar',
                'status'        => 'successful',
            ],
        ]);

        $data = $this->client->JobStatuses->JobStatus->returnAJobStatus($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------
}
