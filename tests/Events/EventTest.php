<?php

namespace Tests\Events;

use Exception;
use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Event Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class EventTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetEventList(): void
    {
        $data = $this->client->Events->Event->getEventsList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetEvent(): void
    {
        $params = ['id' => 'ejom4k3o5qor5ooyh8yx7hgbw'];

        $data = $this->client->Events->Event->getEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddEvent(): void
    {
        $params =
        [
            'calendar_id' => '1fskeosmvaffwuffq774enx5p',
            'when'        => ['time' => \time()],
            'title'       => 'nothing...',
        ];

        $data = $this->client->Events->Event->addEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateEvent(): void
    {
        $params =
        [
            'id'      => '47137b6urkg0cf738o7is2aa3',
            'when'    => ['time' => \time()],
        ];

        $data = $this->client->Events->Event->updateEvent($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteDraft(): void
    {
        $params =
        [
            'id' => '47137b6urkg0cf738o7is2aa3',
        ];

        try
        {
            $back = true;
            $this->client->Events->Event->deleteEvent($params);
        }
        catch (Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------
}
