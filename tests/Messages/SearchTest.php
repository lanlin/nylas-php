<?php

declare(strict_types = 1);

namespace Tests\Messages;

use JsonException;
use Tests\AbsCase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * ----------------------------------------------------------------------------------
 * Message Search Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/21
 *
 * @internal
 */
class SearchTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testSearchMessage(): void
    {
        $q = 'testing';

        $this->mockResponse($this->getSearchData());

        $data = $this->client->Messages->Search->searchMessages($q);

        static::assertArrayHasKey('account_id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array[]
     */
    private function getSearchData(): array
    {
        return [[
            'account_id'          => '43jf3n4es3***',
            'bcc'                 => [],
            'body'                => 'Hello, how are you?',
            'cc'                  => [],
            'date'                => 1559770299,
            'events'              => [],
            'files'               => [],
            'folder'              => ['display_name' => 'Draft', 'id' => 'eeangfw9vm5***', 'name' => 'drafts'],
            'from'                => [['email' => 'nylastest***@yahoo.com', 'name' => 'John Doe']],
            'id'                  => '27hvuc1w2v85***',
            'object'              => 'draft',
            'reply_to'            => [],
            'reply_to_message_id' => null,
            'snippet'             => 'Hello, how are you?',
            'starred'             => false,
            'subject'             => 'ugh?',
            'thread_id'           => '3sso5z8gb3***',
            'to'                  => [['email' => '[[email]]', 'name' => '[[name]]']],
            'unread'              => false,
            'version'             => 0,
        ], [
            'account_id'          => '43jf3n4es3i***',
            'bcc'                 => [],
            'body'                => 'Hello, how are you?',
            'cc'                  => [],
            'date'                => 1559762902,
            'events'              => [],
            'files'               => [],
            'folder'              => ['display_name' => 'Draft', 'id' => 'eeangfw9vm5j4f***', 'name' => 'drafts'],
            'from'                => [['email' => 'nylastest***@yahoo.com', 'name' => 'John Doe']],
            'id'                  => '92c7gucghzh16147dpluw1q2d',
            'object'              => 'draft',
            'reply_to'            => [],
            'reply_to_message_id' => null,
            'snippet'             => 'Hello, how are you?',
            'starred'             => false,
            'subject'             => 'Hello',
            'thread_id'           => 'e48pmw615r***',
            'to'                  => [['email' => '[[email]]', 'name' => '[[name]]']],
            'unread'              => false,
            'version'             => 0,
        ]];
    }

    // ------------------------------------------------------------------------------
}
