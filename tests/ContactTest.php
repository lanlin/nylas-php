<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Contacts Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2020/04/26
 */
class ContactTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetContactsList() : void
    {
        $data = self::$api->Contacts()->Contact()->getContactsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetContact() : void
    {
        $id = 'p8yaokbz6oh8bd45jcs1vt74';

        $data = self::$api->Contacts()->Contact()->getContact($id);

        $this->assertArrayHasKey($id, $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddContact() : void
    {
        $params =
        [
            'company_name' => null,
            'given_name' => 'Bown',
            'surname' => 'Chou',
        ];

        $data = self::$api->Contacts()->Contact()->addContact($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testUpdateContact() : void
    {
        $params =
        [
            'id'           => 'ojuzyfudlwkrwg476ip9sfw4',
            'company_name' => 'testing',
            'given_name' => 'Gege',
            'surname' => 'Chou',
        ];

        $data = self::$api->Contacts()->Contact()->updateContact($params);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testDeleteContact() : void
    {
        $params =
        [
            'company_name' => null,
            'given_name' => 'Bown',
            'surname' => 'Chou',
        ];

        $data = self::$api->Contacts()->Contact()->addContact($params);


        try
        {
            $back = true;
            self::$api->Contacts()->Contact()->deleteContact($data['id']);
        }
        catch (\Exception $e)
        {
            $back = false;
        }

        $this->assertTrue($back);
    }

    // ------------------------------------------------------------------------------

    public function testGetContactGroups() : void
    {
        $data = self::$api->Contacts()->Contact()->getContactGroups();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetContactPicture() : void
    {
        $params =
        [
            'id'   => 'ojuzyfudlwkrwg476ip9sfw4',
            'path' => __DIR__. '/temp',
        ];


        $data = self::$api->Contacts()->Contact()->getContactPicture($params);

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

}
