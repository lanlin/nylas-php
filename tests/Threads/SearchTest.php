<?php

namespace Tests\Threads;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Thread Search Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/07/21
 *
 * @internal
 */
class SearchTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testSearchThread(): void
    {
        $q = 'test@test.com';

        $data = $this->client->Threads->Search->threads($q);

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    private function getThreadBaseData(): array
    {
        return [
            'id'              => '[thread_id]',
            'object'          => 'thread',
            'snippet'         => 'Hi James, welcome.',
            'starred'         => true,
            'subject'         => 'Security settings changed on your Yahoo account',
            'unread'          => true,
            'version'         => 1,
            'labels'          => ['string'],
            'draft_ids'       => ['string'],
            'account_id'      => '[account_id]',
            'message_ids'     => ['string'],
            'has_attachments' => true,
            'folders'         => [['id' => '[folder_id]', 'name' => 'inbox', 'display_name' => 'Inbox']],
            'participants'    => [['name' => 'Yahoo', 'email' => 'no-reply@cc.yahoo-inc.com']],

            'last_message_timestamp'          => 1557950729,
            'first_message_timestamp'         => 1557950729,
            'last_message_sent_timestamp'     => 0,
            'last_message_received_timestamp' => 1557950729,
        ];
    }

    // ------------------------------------------------------------------------------
}
