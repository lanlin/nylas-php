<?php

namespace Tests\Threads;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Thread Search Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class SearchTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testSearchThread(): void
    {
        $q = 'test@test.com';

        $this->mockResponse($this->getSearchData());

        $data = $this->client->Threads->Search->threads($q);

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    private function getSearchData(): array
    {
        return [[
            'account_id'                      => '43jf3n4es3i***',
            'draft_ids'                       => ['27hvuc1w2v85***'],
            'first_message_timestamp'         => 1559770299,
            'folders'                         => [['display_name' => 'Draft', 'id' => 'eeangfw9vm5j***', 'name' => 'drafts']],
            'has_attachments'                 => false,
            'id'                              => '3sso5z8gb3ts***',
            'last_message_received_timestamp' => null,
            'last_message_sent_timestamp'     => null,
            'last_message_timestamp'          => 1559770299,
            'message_ids'                     => [],
            'object'                          => 'thread',
            'participants'                    => [],
            'snippet'                         => '',
            'starred'                         => false,
            'subject'                         => 'ugh?',
            'unread'                          => false,
            'version'                         => 1,
        ], [
            'account_id'                      => '43jf3n4es3i***',
            'draft_ids'                       => ['92c7gucghzh***'],
            'first_message_timestamp'         => 1559762902,
            'folders'                         => [['display_name' => 'Draft', 'id' => 'eeangfw9vm5***', 'name' => 'drafts']],
            'has_attachments'                 => false,
            'id'                              => 'e48pmw615r2i***',
            'last_message_received_timestamp' => null,
            'last_message_sent_timestamp'     => null,
            'last_message_timestamp'          => 1559762902,
            'message_ids'                     => [],
            'object'                          => 'thread',
            'participants'                    => [],
            'snippet'                         => '',
            'starred'                         => false,
            'subject'                         => 'Hello',
            'unread'                          => false,
            'version'                         => 1,
        ]];
    }

    // ------------------------------------------------------------------------------
}
