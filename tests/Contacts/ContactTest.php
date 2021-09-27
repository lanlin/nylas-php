<?php

namespace Tests\Contacts;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Contacts Test
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class ContactTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    public function testReturnAllContacts(): void
    {
        $this->mockResponse([
            [
                'account_id'   => '5tgncdmczat02216u7d6uypyi',
                'birthday'     => null,
                'company_name' => null,
                'emails'       => [
                    [
                        'email' => 'tom@brightideas.com',
                        'type'  => 'other',
                    ],
                ],
                'given_name'         => 'Thomas',
                'groups'             => [],
                'id'                 => '7e1b9vqhzyjn05y22sdoxl9ij',
                'im_addresses'       => [],
                'job_title'          => null,
                'manager_name'       => null,
                'middle_name'        => null,
                'nickname'           => null,
                'notes'              => null,
                'object'             => 'contact',
                'office_location'    => null,
                'phone_numbers'      => [],
                'physical_addresses' => [],
                'picture_url'        => 'https://api.nylas.com/contacts/7vqhzyjn05y22sdoxl9ij/picture',
                'source'             => 'address_book',
                'suffix'             => null,
                'surname'            => 'Edison',
                'web_pages'          => [],
            ],
        ]);

        $data = $this->client->Contacts->Contact->returnAllContacts();

        $this->assertArrayHasKey('id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    public function testCreateAContact(): void
    {
        $params = $this->getContactBase();

        $this->mockResponse($this->getContactData());

        $data = $this->client->Contacts->Contact->createAContact($params);

        $this->assertArrayHasKey('groups', $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnAContact(): void
    {
        $id = 'z3z3z3z3z3z3z3z3z3z3z3';

        $this->mockResponse($this->getContactData());

        $data = $this->client->Contacts->Contact->returnAContact($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateAContact(): void
    {
        $id = 'z3z3z3z3z3z3z3z3z3z3z3';

        $params = $this->getContactBase();

        $this->mockResponse($this->getContactData());

        $data = $this->client->Contacts->Contact->updateAContact($id, $params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteAContact(): void
    {
        $id = 'z3z3z3z3z3z3z3z3z3z3z3';

        $this->mockResponse([]);

        $data = $this->client->Contacts->Contact->deleteAContact($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnsAContactsPicture(): void
    {
        $params = [
            'id'   => 'ojuzyfudlwkrwg476ip9sfw4',
            'path' => __DIR__.'/temp',
        ];

        $this->mockResponse([]);

        $data = $this->client->Contacts->Contact->returnsAContactsPicture($params);

        $this->assertArrayHasKey($params['id'], $data);
    }

    // ------------------------------------------------------------------------------

    public function testReturnContactGroups(): void
    {
        $this->mockResponse([
            [
                'id'         => 'a0a0a0a0a0a0a0a0a0a0a0',
                'object'     => 'contact_group',
                'account_id' => 'x2x2x2x2x2x2x2x2x2x2x2',
                'name'       => 'Work',
                'path'       => 'Contacts/Work',
            ],
        ]);

        $data = $this->client->Contacts->Contact->returnContactGroups();

        $this->assertArrayHasKey('id', $data[0]);
    }

    // ------------------------------------------------------------------------------

    private function getContactBase(): array
    {
        return [
            'birthday'     => '1960-12-31',
            'company_name' => 'Nylas',
            'emails'       => [
                [
                    'email' => 'john@doe.com',
                    'type'  => 'work',
                ],
            ],
            'given_name'   => 'John',
            'im_addresses' => [
                [
                    'type'       => 'aim',
                    'im_address' => 'myaimaddress',
                ],
            ],
            'job_title'       => 'Software Engineer',
            'manager_name'    => 'Bill the manager',
            'middle_name'     => 'Jacob',
            'nickname'        => 'JD',
            'notes'           => 'Loves ramen',
            'office_location' => '123 Main Street',
            'phone_numbers'   => [
                [
                    'number' => '1 800 123 4567',
                    'type'   => 'business',
                ],
            ],
            'physical_addresses' => [
                [
                    'format'         => 'string',
                    'type'           => 'work',
                    'street_address' => 'string',
                    'city'           => 'string',
                    'postal_code'    => 'string',
                    'state'          => 'string',
                    'country'        => 'string',
                ],
            ],
            'suffix'    => 'string',
            'surname'   => 'string',
            'web_pages' => [
                [
                    'type' => 'profile',
                    'url'  => 'string',
                ],
            ],
            'group' => 'string',
        ];
    }

    // ------------------------------------------------------------------------------

    private function getContactData(): array
    {
        return [
            'account_id'   => 'x2x2x2x2x2x2x2x2x2x2x2',
            'birthday'     => '1960-12-31',
            'company_name' => 'Nylas',
            'emails'       => [
                [
                    'email' => 'john@doe.com',
                    'type'  => 'work',
                ],
            ],
            'given_name'   => 'John',
            'id'           => 'z3z3z3z3z3z3z3z3z3z3z3',
            'im_addresses' => [
                [
                    'type'       => 'aim',
                    'im_address' => 'myaimaddress',
                ],
            ],
            'job_title'       => 'Software Engineer',
            'manager_name'    => 'Bill the manager',
            'middle_name'     => 'Jacob',
            'nickname'        => 'JD',
            'notes'           => 'Loves ramen',
            'object'          => 'contact',
            'office_location' => '123 Main Street',
            'phone_numbers'   => [
                [
                    'number' => '1 800 123 4567',
                    'type'   => 'business',
                ],
            ],
            'physical_addresses' => [
                [
                    'format'         => 'string',
                    'type'           => 'work',
                    'street_address' => 'string',
                    'city'           => 'string',
                    'postal_code'    => 'string',
                    'state'          => 'string',
                    'country'        => 'string',
                ],
            ],
            'picture_url' => 'https://api.nylas.com/contacts/427abc427abc427abc/picture',
            'suffix'      => 'string',
            'surname'     => 'string',
            'web_pages'   => [
                [
                    'type' => 'profile',
                    'url'  => 'string',
                ],
            ],
            'groups' => [
                [
                    'id'         => 'string',
                    'object'     => 'contact_group',
                    'account_id' => 'string',
                    'name'       => 'string',
                    'path'       => 'string',
                ],
            ],
        ];
    }

    // ------------------------------------------------------------------------------
}
