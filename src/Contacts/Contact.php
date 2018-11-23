<?php namespace Nylas\Contacts;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Contacts
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Contact
{

    // ------------------------------------------------------------------------------

    /**
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    // ------------------------------------------------------------------------------

    /**
     * get contacts list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getContactsList(array $params)
    {
        if (!$this->getBaseRules()->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getContact(array $params)
    {
        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add contact
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addContact(array $params)
    {
        $rules = $this->addContactRules();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update contact
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function updateContact(array $params)
    {
        $rules = $this->addContactRules();

        array_push($rules,  V::key('id', V::stringType()::notEmpty()));

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->request
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete contact
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteContact(array $params)
    {
        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact groups
     *
     * @param string $accessToken
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getContactGroups(string $accessToken)
    {
        if (!V::stringType()::notEmpty()->validate($accessToken))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $accessToken];

        return $this->request->setHeaderParams($header)->get(API::LIST['contactsGroups']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact picture
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getContactPicture(array $params)
    {
        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty())
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        return $this->request
        ->setPath($path)
        ->setHeaderParams($header)
        ->get(API::LIST['contactPic']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return \Respect\Validation\Validator
     */
    private function getBaseRules()
    {
        return V::keySet(
            V::key('limit', V::intType()::min(1), false),
            V::key('offset', V::intType()::min(0), false),

            V::key('email', V::email(), false),
            V::key('state', V::stringType()::notEmpty(), false),
            V::key('group', V::stringType()::notEmpty(), false),
            V::key('source', V::stringType()::notEmpty(), false),
            V::key('country', V::stringType()::notEmpty(), false),

            V::key('recurse', V::boolType(), false),
            V::key('postal_code', V::stringType()::notEmpty(), false),
            V::key('phone_number', V::stringType()::notEmpty(), false),
            V::key('street_address', V::stringType()::notEmpty(), false),

            V::key('access_token', V::stringType()::notEmpty())
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for add contact
     *
     * @return array
     */
    private function addContactRules()
    {
        return
        [
            V::key('given_name', V::stringType()::notEmpty(), false),
            V::key('middle_name', V::stringType()::notEmpty(), false),
            V::key('surname', V::stringType()::notEmpty(), false),
            V::key('birthday', V::date('c'), false),
            V::key('suffix', V::stringType()::notEmpty(), false),
            V::key('nickname', V::stringType()::notEmpty(), false),
            V::key('company_name', V::stringType()::notEmpty(), false),
            V::key('job_title', V::stringType()::notEmpty(), false),

            V::key('manager_name', V::stringType()::notEmpty(), false),
            V::key('office_location', V::stringType()::notEmpty(), false),
            V::key('notes', V::stringType()::notEmpty(), false),
            V::key('emails', V::arrayVal()->each(V::email()), false),

            V::key('im_addresses', V::arrayType(), false),
            V::key('physical_addresses', V::arrayType(), false),
            V::key('phone_numbers', V::arrayType(), false),
            V::key('web_pages', V::arrayType(), false),

            V::key('access_token', V::stringType()::notEmpty())
        ];
    }

    // ------------------------------------------------------------------------------

}
