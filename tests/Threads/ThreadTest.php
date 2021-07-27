<?php

namespace Tests\Threads;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Thread Test
 * ----------------------------------------------------------------------------------
 *
 * @link https://developer.nylas.com/docs/api/#post/labels
 * @author lanlin
 * @change 2021/07/21
 *
 * @internal
 */
class ThreadTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetThreadList(): void
    {
        $para = [
            'to'                  => $this->faker->email,
            'cc'                  => $this->faker->email,
            'bcc'                 => $this->faker->email,
            'from'                => $this->faker->email,
            'any_email'           => $this->faker->email,
            'in'                  => $this->faker->randomElement(['name', 'display_name', 'id']),
            'not_in'              => $this->faker->word,
            'view'                => $this->faker->randomElement(['ids', 'count', 'expanded']),
            'limit'               => 100,
            'offset'              => 0,
            'subject'             => $this->faker->title,
            'unread'              => $this->faker->boolean(),
            'starred'             => $this->faker->boolean(),
            'filename'            => $this->faker->name(),
            'started_after'       => $this->faker->unixTime,
            'started_before'      => $this->faker->unixTime,
            'last_message_after'  => $this->faker->unixTime,
            'last_message_before' => $this->faker->unixTime,
        ];

        $this->mockResponse([$this->getThreadBaseData()]);

        $data = $this->client->Threads->Thread->getThreadsList($para);

        $this->assertTrue(!empty($data[0]['id']));
    }

    // ------------------------------------------------------------------------------

    public function testGetThread(): void
    {
        $id = $this->faker->uuid;

        $this->mockResponse([$id => $this->getThreadBaseData()]);

        $data = $this->client->Threads->Thread->getThread($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateThread(): void
    {
        $id = $this->faker->uuid;

        $params = [
            'unread'    => $this->faker->randomElement([true, false]),
            'starred'   => $this->faker->randomElement([true, false]),
            'folder_id' => $this->faker->uuid,
            'label_ids' => [$this->faker->uuid],
        ];

        $this->mockResponse($this->getThreadBaseData());

        $data = $this->client->Threads->Thread->updateThread($id, $params);

        $this->assertArrayHasKey('id', $data);
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
