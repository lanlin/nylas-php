<?php

declare(strict_types = 1);

namespace Tests\Messages;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Message Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class MessageTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnAllMessages(): void
    {
        $para = [
            'to'              => $this->faker->email,
            'cc'              => $this->faker->email,
            'bcc'             => $this->faker->email,
            'from'            => $this->faker->email,
            'any_email'       => $this->faker->email,
            'in'              => 'display_name',
            'view'            => 'ids',
            'limit'           => 100,
            'offset'          => 0,
            'subject'         => $this->faker->title,
            'unread'          => $this->faker->boolean(),
            'starred'         => $this->faker->boolean(),
            'thread_id'       => $this->faker->uuid,
            'filename'        => $this->faker->name(),
            'received_after'  => $this->faker->unixTime(),
            'received_before' => $this->faker->unixTime(),
            'has_attachment'  => true,
        ];

        $this->mockResponse($this->getMessageBaseData());

        $data = $this->client->Messages->Message->returnAllMessages($para);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testReturnAMessage(): void
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $this->mockResponse($this->getMessageBaseData());

        $data = $this->client->Messages->Message->returnAMessage($id);

        static::assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testReturnARawMessage(): void
    {
        $id = 'eyhcafxtzkke6tfsdo9g92utb';

        $this->mockResponse($this->getMessageBaseData()[0]);

        $data = $this->client->Messages->Message->returnARawMessage($id);

        static::assertIsObject($data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testUpdateAMessage(): void
    {
        $id = $this->faker->uuid;

        $params = [
            'unread'    => $this->faker->randomElement([true, false]),
            'starred'   => $this->faker->randomElement([true, false]),
            'folder_id' => $this->faker->uuid,
            'label_ids' => [$this->faker->uuid],
        ];

        $this->mockResponse($this->getMessageBaseData()[0]);

        $data = $this->client->Messages->Message->updateAMessage($id, $params);

        static::assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array[]
     */
    private function getMessageBaseData(): array
    {
        return [[
            'account_id' => '[account_id]',
            'id'         => 'eyhcafxtzkke6tfsdo9g92utb',
            'body'       => '<html>\\n<head>\\n <meta charset=\\"UTF-8\\">\\n <style type=\\"text/css\\">\\n html [\\n -webkit-text-size-adjust =>none;\\n ]\\n body [\\n width =>100%;\\n margin =>0 auto;\\n padding =>0;\\n]\\n  p [\\n width =>280px;\\n line-height => 16px;\\n letter-spacing => 0.5px;\\n ]\\n </style>\\n <title>Welcome  ...  </html>',
            'object'     => 'message',
            'reply_to'   => [['email' => 'skwolek@fibers.com', 'name' => 'Stephanie Kwolek']],
            'snippet'    => 'string',
            'starred'    => true,
            'subject'    => 'string',
            'thread_id'  => 'string',
            'unread'     => true,
            'to'         => [['email' => 'dorothy@spacetech.com', 'name' => 'Dorothy Vaughan']],
            'cc'         => [['email' => 'George Washington Carver', 'name' => 'carver@agritech.com']],
            'bcc'        => [['email' => 'Albert Einstein', 'name' => 'al@particletech.com']],
            'from'       => [['name' => 'Marie Curie', 'email' => 'marie@radioactivity.com']],
            'date'       => 1557950729,
            'events'     => [[]],
            'files'      => [[
                'content_disposition' => 'attachment',
                'content_type'        => 'image/jpeg',
                'filename'            => 'image.jpeg',
                'id'                  => '[image_id]',
                'size'                => 2648890,
            ]],
            'folder' => [
                'display_name' => 'string',
                'id'           => 'string',
                'name'         => 'string',
            ],
            'labels' => [[
                'display_name' => 'Important',
                'id'           => '[label_id]',
                'name'         => 'important',
            ]],
        ]];
    }

    // ------------------------------------------------------------------------------
}
