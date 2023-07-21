<?php

declare(strict_types = 1);

namespace Tests\Deltas;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Delta Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class DeltaTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testGetADeltaCursor(): void
    {
        $this->mockResponse([
            'cursor' => 'aqb0llc2ioo0***',
        ]);

        $data = $this->client->Deltas->Delta->getADeltaCursor();

        static::assertArrayHasKey('cursor', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testRequestDeltaCursors(): void
    {
        $params = ['cursor' => '4whx9f0r544dwd07ipymrj7a1'];

        $this->mockResponse($this->getDeltasData());

        $data = $this->client->Deltas->Delta->requestDeltaCursors($params);

        static::assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnLongPollingDeltas(): void
    {
        $params = ['cursor' => '4whx9f0r544dwd07ipymrj7a1'];

        $this->mockResponse($this->getDeltasData());

        $data = $this->client->Deltas->Delta->returnLongPollingDeltas($params);

        static::assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testStreamingDeltas(): void
    {
        $params = ['cursor' => '4whx9f0r544dwd07ipymrj7a1'];

        $this->mockResponse($this->getDeltasData());

        $data = $this->client->Deltas->Delta->streamingDeltas($params);

        static::assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function getDeltasData(): array
    {
        return [
            'cursor_end'   => '4ekj8ktel67njbaw1c0nlvbdi',
            'cursor_start' => '9fboxh6t9b3ar4fwocxpwrcss',
            'deltas'       => [
                [
                    'attributes' => [
                        'account_id' => 'aaz875kwuvxik6ku7pwkqp3ah',
                        'bcc'        => [],
                        'body'       => 'testing',
                        'cc'         => [],
                        'date'       => 1602001027,
                        'events'     => [],
                        'files'      => [],
                        'from'       => [[
                            'email' => 'email@nylas.com',
                            'name'  => 'Katherine Perry',
                        ]],
                        'id'     => '52m5un5v1m7rjigna5agc7y35',
                        'labels' => [[
                            'display_name' => 'Sent Mail',
                            'id'           => 'ertg5obp5nvn43xtqe2e55en0',
                            'name'         => 'sent',
                        ]],
                        'object'    => 'message',
                        'reply_to'  => [],
                        'snippet'   => 'Hi Katherine PerryLead Technical Writer, Nylasemail@nylas.com',
                        'starred'   => false,
                        'subject'   => 'New Message',
                        'thread_id' => 'chvd75bowkhg3gfpgeeygcxbb',
                        'to'        => [[
                            'email' => 'swag@nylas.com',
                            'name'  => 'Katherine Personal',
                        ]],
                        'unread' => false,
                    ],
                    'cursor' => '8hhvivgus0fbo4qengko8c38x',
                    'event'  => 'create',
                    'id'     => '52m5un5v1m7rjigna5agc7y35',
                    'object' => 'message',
                ],
            ],
        ];
    }

    // ------------------------------------------------------------------------------
}
