<?php

namespace Tests\Drafts;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Draft Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class DraftTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllDrafts(): void
    {
        $this->mockResponse([$this->getDraftData()]);

        $data = $this->client->Drafts->Draft->returnAllDrafts();

        $this->assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testCreateADraft(): void
    {
        $params = $this->getDraftBase();

        $this->mockResponse($this->getDraftData());

        $data = $this->client->Drafts->Draft->createADraft($params);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnADraft(): void
    {
        $id = '2vgnewhaclx4iog2140bva9y8';

        $this->mockResponse($this->getDraftData());

        $data = $this->client->Drafts->Draft->returnADraft($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateADraft(): void
    {
        $id = '2vgnewhaclx4iog2140bva9y8';

        $mock = $this->getDraftBase();

        $mock['version'] = 2;

        $this->mockResponse($this->getDraftData());

        $data = $this->client->Drafts->Draft->updateADraft($id, $mock);

        $this->assertArrayHasKey('account_id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteADraft(): void
    {
        $params = ['id' => '2vgnewhaclx4iog2140bva9y8', 'version' => 2];

        $this->mockResponse([]);

        $data = $this->client->Drafts->Draft->deleteADraft($params);

        $this->assertArrayHasKey($params['id'], $data);
    }

    // ------------------------------------------------------------------------------

    private function getDraftBase(): array
    {
        return [
            'subject' => 'From Nylas',
            'to'      => [
                [
                    'name'  => 'Dorothy Vaughan',
                    'email' => 'dorothy@spacetech.com',
                ],
            ],
            'cc' => [
                [
                    'name' => 'George Washington Carver',
                    'email' => 'carver@agritech.com',
                ],
            ],
            'bcc' => [
                [
                    'name' => 'Albert Einstein',
                    'email'  => 'al@particletech.com',
                ],
            ],
            'from' => [
                [
                    'name'  => 'Marie Curie',
                    'email' => 'marie@radioactivity.com',
                ],
            ],
            'reply_to' => [
                [
                    'email' => 'skwolek@fibers.com',
                    'name'  => 'Stephanie Kwolek',
                ],
            ],
            'reply_to_message_id' => '5ko445dyrr4s5pqc6n0klxhg0',
            'body'                => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
            'file_ids'            => ['string'],
        ];
    }

    // ------------------------------------------------------------------------------

    private function getDraftData(): array
    {
        return [
            'account_id' => '{{account_id}}',
            'bcc'        => [
                [
                    'email' => 'string',
                    'name'  => 'string',
                ],
            ],
            'body' => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
            'cc'   => [
                [
                    'email' => 'string',
                    'name'  => 'string',
                ],
            ],
            'date'   => 1623080724,
            'events' => [
            ],
            'files' => [
                [
                    'content_disposition' => 'attachment',
                    'content_type'        => 'image/png',
                    'filename'            => 'Screen Shot 2021-06-01 at 5.06.47 PM.png',
                    'id'                  => '{{file_id}}',
                    'size'                => 82186,
                ],
            ],
            'folder' => [
                'display_name' => 'Drafts',
                'id'           => '5i4pj87birnlrno7s9xu9f5cl',
                'name'         => 'drafts',
            ],
            'from' => [
                [
                    'email' => 'Your Name',
                    'name'  => 'you@example.com',
                ],
            ],
            'id'            => '2vgnewhaclx4iog2140bva9y8',
            'job_status_id' => [
                'account_id'    => '88q8rglxj7jaeneiykhll23e2',
                'action'        => 'save_draft',
                'created_at'    => 1623080724,
                'id'            => '2vgnewhaclx4iog2140bva9y8',
                'job_status_id' => 'ar22f9bwfboc4afyhnmfnuxc8',
                'object'        => 'message',
                'status'        => 'pending',
            ],
            'object'   => 'draft',
            'reply_to' => [
                [
                    'email' => 'swag@nylas.com',
                    'name'  => 'Nylas',
                ],
            ],
            'reply_to_message_id' => '5ko445dyrr4s5pqc6n0klxhg0',
            'snippet'             => 'This email was sent using the Nylas email API. Visit https://nylas.com for details.',
            'starred'             => false,
            'subject'             => 'From Nylas',
            'thread_id'           => '43va79g3vpq2z0ojwq09jg5el',
            'to'                  => [
                [
                    'email' => 'swag@nylas.com',
                    'name'  => 'Nylas',
                ],
            ],
            'unread'  => false,
            'version' => 0,
        ];
    }

    // ------------------------------------------------------------------------------
}
