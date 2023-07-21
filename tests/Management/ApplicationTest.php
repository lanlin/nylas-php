<?php

declare(strict_types = 1);

namespace Tests\Management;

use function time;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Manage Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class ApplicationTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnApplicationIPAddresses(): void
    {
        $this->mockResponse([
            'updated_at'   => 1544658529,
            'ip_addresses' => [
                '52.25.153.17',
                '52.26.120.161',
                '52.39.252.208',
                '54.71.62.98',
                '34.208.138.149',
                '52.88.199.110',
                '54.69.11.122',
                '54.149.110.158',
            ],
        ]);

        $data = $this->client->Management->Application->returnApplicationIPAddresses();

        static::assertArrayHasKey('updated_at', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnApplicationDetails(): void
    {
        $this->mockResponse([
            'application_name' => 'Moon Indigo ✨',
            'icon_url'         => 'https://inbox-developer-resources.s3.amazonaws.com/icons/da5b3a1c-448c-11e7-872b-0625ca014fd6',
            'redirect_uris'    => [
                'http://localhost:5555/login_callback',
                'localhost',
            ],
        ]);

        $data = $this->client->Management->Application->returnApplicationDetails();

        static::assertArrayHasKey('application_name', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateApplicationDetails(): void
    {
        $this->mockResponse([
            'application_name' => 'string',
            'icon_url'         => 'https://inbox-developer-resources.s3.amazonaws.com/icons/da5b3a1c-448c-11e7-872b-0625ca014fd6',
            'redirect_uris'    => ['string'],
        ]);

        $param = [
            'application_name' => 'test_'.time(),
            'redirect_uris'    => ['http://www.test-nylas-test.com'],
        ];

        $data = $this->client->Management->Application->updateApplicationDetails($param);

        static::assertArrayHasKey('application_name', $data);
    }

    // ------------------------------------------------------------------------------
}
