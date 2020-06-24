<?php

namespace Nylas\Tests;

use Exception;

/**
 * ----------------------------------------------------------------------------------
 * Contacts Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 *
 * @internal
 */
class ContactTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testGetContactsList(): void
    {
        $data = $this->client->Contacts()->Contact()->getContactsList();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetContact(): void
    {
        $id = 'p8yaokbz6oh8bd45jcs1vt74';

        $data = $this->client->Contacts()->Contact()->getContact($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddContact(): void
    {
        $params = $this->getContactInfo();

        $data = $this->client->Contacts()->Contact()->addContact($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateContact(): void
    {
        $params =
        [
            'id'           => 'ojuzyfudlwkrwg476ip9sfw4',
            'company_name' => 'testing',
            'given_name'   => 'Gege',
            'surname'      => 'Chou',
        ];

        $data = $this->client->Contacts()->Contact()->updateContact($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteContact(): void
    {
        $params =
        [
            'company_name' => null,
            'given_name'   => 'Bown',
            'surname'      => 'Chou',
        ];

        $data = $this->client->Contacts()->Contact()->addContact($params);

        try
        {
            $back = true;
            $this->client->Contacts()->Contact()->deleteContact($data['id']);
        }
        catch (Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------

    public function testGetContactGroups(): void
    {
        $data = $this->client->Contacts()->Contact()->getContactGroups();

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetContactPicture(): void
    {
        $params =
        [
            'id'   => 'ojuzyfudlwkrwg476ip9sfw4',
            'path' => __DIR__.'/temp',
        ];

        $data = $this->client->Contacts()->Contact()->getContactPicture($params);

        $this->assertTrue(\count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    /**
     * @return array
     */
    private function getContactInfo(): array
    {
        return [
            'given_name'     => 'My',
            'middle_name'    => 'Nylas',
            'surname'        => 'Friend',
            'birthday'       => '2014-06-01',
            'suffix'         => 'API',
            'nickname'       => 'Nylas',
            'company_name'   => 'Nylas',
            'job_title'      => 'Communications Platform',
            'manager_name'   => 'Communications',
            'office_location'=> 'SF',
            'notes'          => 'Check out the Nylas Email, Calendar, and Contacts APIs',
            'emails'         => [[
                'type' => 'personal',
                'email'=> 'swagg@nylas.com',
            ]],
            'physical_addresses'=> [[
                'type'          => 'work',
                'street_address'=> '944 Market St, 8th Floor',
                'city'          => 'San Francisco',
                'postal_code'   => '94102',
                'state'         => 'CA',
                'country'       => 'USA',
            ]],
            'phone_numbers'=> [[
                'type'  => 'home',
                'number'=> '123-123-1234',
            ]],
            'web_pages'=> [[
                'type'=> 'homepage',
                'url' => 'https=>//nylas.com',
            ]],
            'im_addresses'=> [[
                'type'      => 'gtalk',
                'im_address'=> 'Nylas',
            ]],
        ];
    }

    // ------------------------------------------------------------------------------
}
