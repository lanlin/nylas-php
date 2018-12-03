<?php namespace NylasTest;

/**
 * ----------------------------------------------------------------------------------
 * Contacts Test
 * ----------------------------------------------------------------------------------
 *
 * @update lanlin
 * @change 2018/11/28
 */
class ContactTest extends Abs
{

    // ------------------------------------------------------------------------------

    public function testGetContactsList()
    {
        $data = self::$api->Contacts()->Contact()->getContactsList();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetContact()
    {
        $id = 'p8yaokbz6oh8bd45jcs1vt74';

        $data = self::$api->Contacts()->Contact()->getContact($id);

        $this->assertArrayHasKey('id', $data);
    }

    // ------------------------------------------------------------------------------

    public function testAddContact()
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

    public function testUpdateContact()
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

    public function testDeleteContact()
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

    public function testGetContactGroups()
    {
        $data = self::$api->Contacts()->Contact()->getContactGroups();

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

    public function testGetContactPicture()
    {
        $params =
        [
            'id'   => 'ojuzyfudlwkrwg476ip9sfw4',
            'path' => dirname(__FILE__). '/temp',
        ];


        $data = self::$api->Contacts()->Contact()->getContactPicture($params);

        $this->assertTrue(count($data) > 0);
    }

    // ------------------------------------------------------------------------------

}
