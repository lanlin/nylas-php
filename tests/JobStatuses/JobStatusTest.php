<?php

declare(strict_types = 1);

namespace Tests\JobStatuses;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * JobStatus Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class JobStatusTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
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

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
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

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------
}
