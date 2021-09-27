<?php

namespace Tests\Deltas;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Delta Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class DeltaTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetADeltaCursor(): void
    {
        $this->mockResponse([
            'cursor' => 'aqb0llc2ioo0***',
        ]);

        $data = $this->client->Deltas->Delta->getADeltaCursor();

        $this->assertArrayHasKey('cursor', $data);
    }

    // ------------------------------------------------------------------------------

    public function testRequestDeltaCursors(): void
    {
        $params = ['cursor' => '4whx9f0r544dwd07ipymrj7a1'];

        $this->mockResponse($this->getDeltasData());

        $data = $this->client->Deltas->Delta->requestDeltaCursors($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnLongPollingDeltas(): void
    {
        $params = ['cursor' => '4whx9f0r544dwd07ipymrj7a1'];

        $this->mockResponse($this->getDeltasData());

        $data = $this->client->Deltas->Delta->returnLongPollingDeltas($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

    public function testStreamingDeltas(): void
    {
        $params = ['cursor' => '4whx9f0r544dwd07ipymrj7a1'];

        $this->mockResponse($this->getDeltasData());


        $data = $this->client->Deltas->Delta->streamingDeltas($params);

        $this->assertArrayHasKey('deltas', $data);
    }

    // ------------------------------------------------------------------------------

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
