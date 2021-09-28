<?php

namespace Tests\Neural;

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
class ConversationTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testCleanConversation(): void
    {
        $id = $this->faker->uuid;

        $params = [
            'ignore_links'              => true,
            'ignore_images'             => true,
            'ignore_tables'             => true,
            'remove_conclusion_phrases' => true,
            'images_as_markdown'        => true,
        ];

        $this->mockResponse($this->getMessageData());

        $data = $this->client->Neural->Conversation->cleanConversation($id, $params);

        $this->assertArrayHasKey('conversation', $data);
    }

    // ------------------------------------------------------------------------------

    public function testCleanConversationsFeedback(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([[
            'model_version' => 'string',
            'message_id'    => 'string',
            'feedback_at'   => 'string',
        ]]);

        $data = $this->client->Neural->Conversation->cleanConversationsFeedback($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    private function getMessageData(): array
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
            'events' => [[]],
            'files'  => [
                [
                    'account_id'   => '43jf3n4es3***',
                    'content_type' => 'image/jpeg',
                    'filename'     => 'image.jpg',
                    'id'           => '9etjh6talp***',
                    'object'       => 'file',
                    'size'         => 72379,
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
            'model_version' => 'd6d33291',
            'conversation'  => "__\n\nVirtual calendars are now included with Nylas Platform and Calendar\nsubscriptions.\n\n[ ![Logo](https://12qf1516hja245v1v537ieww-wpengine.netdna-ssl.com/wp-\ncontent/uploads/2019/08/group-14@3x.png)\n](https://email.nylas.com/CSQ00K0mV0NL03BE3O02W06)  \n\n---  \n\n[\n![Virtual_Calendar_NewsletterHero@2x.png](https://info.nylas.com/rs/857-LSW-455/images/Virtual_Calendar_NewsletterHero%402x.png)\n](https://email.nylas.com/x0L0000N3n20V3S0LB6QEWO)  \n\n---  \n|  \n---  \n\nBuild flexible, secure scheduling features with just a few lines of code.\nVirtual Calendars eliminate the need for your users to authenticate with their\npersonal calendars and provide all the seamless scheduling features your users\nlove.  \n\n  \n|  | [READ MORE](https://email.nylas.com/x0L0000N3n20V3S0LB6QEWO)  \n---  \n|\n![Inbox_Zero_SQ@2x.png](https://info.nylas.com/rs/857-LSW-455/images/Inbox_Zero_SQ%402x.png)\n\nInstantly connect 100% of email accounts with Hosted Auth.\n\n[READ MORE](https://email.nylas.com/K00O6LS00VNo3MBEQ0230W0)\n\n|\n![Privacy_Shield_Ruling_SQ@2x.png](https://info.nylas.com/rs/857-LSW-455/images/Privacy_Shield_Ruling_SQ%402x.png)\n\nLearn what the recent Privacy Shield Ruling means for your users' data.\n\n[READ MORE](https://email.nylas.com/wSEW36BLV0000Q2N00O3p0N)",
        ];
    }

    // ------------------------------------------------------------------------------
}
