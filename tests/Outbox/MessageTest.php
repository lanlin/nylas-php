<?php

namespace Tests\Outbox;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Message Sending Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/28
 *
 * @internal
 */
class MessageTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllMessagesToBeSent(): void
    {
        $this->mockResponse($this->getMessagesData());

        $data = $this->client->Outbox->Message->returnAllMessagesToBeSent();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testSendAMessage(): void
    {
        $params = [
            'subject' => 'Welcome to Nylas!',
            'send_at' => 1608156000,
            'to'      => [
                [
                    'email' => 'dorothy@spacetech.com',
                    'name'  => 'Dorothy',
                ],
            ],
            'from' => [
                [
                    'name'  => 'Katherine',
                    'email' => 'kat@spacetech.com',
                ],
            ],
            'body' => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
        ];

        $this->mockResponse($this->getMessagesData()[0]);

        $data = $this->client->Outbox->Message->sendAMessage($params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateSendTime(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([
            'account_id'    => '5tgncdmczat02216u7d6uypyi',
            'action'        => 'outbox',
            'created_at'    => 1608065541,
            'id'            => '8bup1y1szsybrj91e86l9l07o',
            'job_status_id' => '996mfx5bg5yzay4bpedug7of2',
            'object'        => 'message',
            'send_at'       => 1608155100,
            'status'        => 'pending',
        ]);

        $data = $this->client->Outbox->Message->updateSendTime($id);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteScheduledMessage(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([]);

        $data = $this->client->Outbox->Message->deleteScheduledMessage($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    private function getMessagesData(): array
    {
        return [
            [
                'account_id' => '5tgncdmczat02216u7d6uypyi',
                'action'     => 'outbox',
                'bcc'        => [],
                'body'       => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
                'cc'         => [],
                'created_at' => 1608064069,
                'date'       => 1608064068,
                'events'     => [],
                'files'      => [],
                'from'       => [
                    [
                        'email' => 'Katherine',
                        'name'  => 'kat@spacetech.com',
                    ],
                ],
                'id'                  => 'afc7gknkgig2dfeu0d7w2llly',
                'job_status_id'       => 'endc1r0qttsrq2et5jkp5unut',
                'labels'              => [],
                'object'              => 'draft',
                'reply_to'            => [],
                'reply_to_message_id' => 'afc7gknkgig2dfeu0d7w2llly',
                'send_at'             => 1608062400,
                'snippet'             => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
                'starred'             => false,
                'status'              => 'pending',
                'subject'             => 'From Nylas',
                'thread_id'           => 'clmeoxat1457kfau28ifp5n8b',
                'to'                  => [
                    [
                        'email' => 'dorothy@spacetech.com',
                        'name'  => 'Dorothy',
                    ],
                ],
                'unread'  => false,
                'version' => 0,
            ],
        ];
    }

    // ------------------------------------------------------------------------------
}
