<?php

namespace Tests\Threads;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Thread Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class ThreadTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetThreadList(): void
    {
        $data = $this->client->Threads()->Thread()->getThreadsList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetThread(): void
    {
        $id = '7ax24gg39w06rqosrda5dtw4w';

        $data = $this->client->Threads()->Thread()->getThread($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateThread(): void
    {
        $params =
        [
            'id'     => '7ax24gg39w06rqosrda5dtw4w',
            'unread' => true,
        ];

        $data = $this->client->Threads()->Thread()->updateThread($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testSearchThread(): void
    {
        $q = 'test@test.com';

        $data = $this->client->Threads()->Search()->threads($q);

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------
}
