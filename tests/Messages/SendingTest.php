<?php

declare(strict_types = 1);

namespace Tests\Messages;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Message Sending Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class SendingTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws JsonException
     */
    public function testSendAnEmailDirectly(): void
    {
        $params = [
            'subject' => 'From Nylas',
            'to'      => [[
                'name'  => 'Nylas',
                'email' => 'swag@nylas.com',
            ]],
            'cc'   => [],
            'bcc'  => [],
            'from' => [[
                'name'  => 'Your Name',
                'email' => 'you@example.com',
            ]],
            'reply_to'            => [],
            'reply_to_message_id' => 'string',
            'body'                => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
            'file_ids'            => ['string'],
        ];

        $this->mockResponse($this->getEmailData());

        $data = $this->client->Messages->Sending->sendAnEmailDirectly($params);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSendRawMimeMessage(): void
    {
        $content = 'testing send raw';

        $this->mockResponse($this->getEmailData());

        $data = $this->client->Messages->Sending->sendRawMiMeMessage($content);

        static::assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function getEmailData(): array
    {
        return [
            'account_id' => '{account_id}',
            'bcc'        => [
                [
                    'email' => 'Albert Einstein',
                    'name'  => 'al@particletech.com',
                ],
            ],
            'body' => "<html>\n<head>\n <meta charset=\"UTF-8\">\n <style type=\"text/css\">\n html {\n -webkit-text-size-adjust:none;\n }\n body {\n width:100%;\n margin:0 auto;\n padding:0;\n}\n  p {\n width:280px;\n line-height: 16px;\n letter-spacing: 0.5px;\n }\n </style>\n <title>Welcome  ...  </html>",
            'cc'   => [
                [
                    'email' => 'George Washington Carver',
                    'name'  => 'carver@agritech.com',
                ],
            ],
            'date'   => 1557950729,
            'events' => [],
            'files'  => [
                [
                    'content_disposition' => 'attachment',
                    'content_type'        => 'image/jpeg',
                    'filename'            => 'image.jpeg',
                    'id'                  => '{image_id}',
                    'size'                => 2648890,
                ],
            ],
            'folder' => [
                'display_name' => 'string',
                'id'           => 'string',
                'name'         => 'string',
            ],
            'from' => [
                [
                    'name'  => 'Marie Curie',
                    'email' => 'marie@radioactivity.com',
                ],
            ],
            'id'       => 'string',
            'object'   => 'message',
            'reply_to' => [
                [
                    'email' => 'skwolek@fibers.com',
                    'name'  => 'Stephanie Kwolek',
                ],
            ],
            'snippet'   => 'string',
            'starred'   => true,
            'subject'   => 'string',
            'thread_id' => 'string',
            'to'        => [
                [
                    'email' => 'dorothy@spacetech.com',
                    'name'  => 'Dorothy Vaughan',
                ],
            ],
            'unread' => true,
            'labels' => [
                [
                    'display_name' => 'Important',
                    'id'           => '{label_id}',
                    'name'         => 'important',
                ],
            ],
        ];
    }

    // ------------------------------------------------------------------------------
}
