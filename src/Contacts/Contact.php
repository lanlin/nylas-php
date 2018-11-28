<?php namespace Nylas\Contacts;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

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
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Contact constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get contacts list
     *
     * @param array $params
     * @return mixed
     */
    public function getContactsList(array $params = [])
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate($this->getBaseRules(), $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact
     *
     * @param string $contactId
     * @param string $accessToken
     * @return mixed
     */
    public function getContact(string $contactId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $contactId,
            'access_token' => $accessToken ?? $this->options->getAccessToken()
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->get(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add contact
     *
     * @param array $params
     * @return mixed
     */
    public function addContact(array $params)
    {
        $rules = $this->addContactRules();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
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
     */
    public function updateContact(array $params)
    {
        $rules = $this->addContactRules();

        array_push($rules,  V::key('id', V::stringType()->notEmpty()));

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete contact
     *
     * @param string $contactId
     * @param string $accessToken
     * @return mixed
     */
    public function deleteContact(string $contactId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $contactId,
            'access_token' => $accessToken ?? $this->options->getAccessToken()
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
        ->setHeaderParams($header)
        ->delete(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact groups
     *
     * @param string $accessToken
     * @return mixed
     */
    public function getContactGroups(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getRequest()
        ->setHeaderParams($header)
        ->get(API::LIST['contactsGroups']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact picture
     *
     * @param string $contactId
     * @param string $accessToken
     * @return mixed
     */
    public function getContactPicture(string $contactId, string $accessToken = null)
    {
        $params =
        [
            'id'           => $contactId,
            'access_token' => $accessToken ?? $this->options->getAccessToken()
        ];

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        return $this->options
        ->getRequest()
        ->setPath($params['id'])
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
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),

            V::keyOptional('email', V::email()),
            V::keyOptional('state', V::stringType()->notEmpty()),
            V::keyOptional('group', V::stringType()->notEmpty()),
            V::keyOptional('source', V::stringType()->notEmpty()),
            V::keyOptional('country', V::stringType()->notEmpty()),

            V::keyOptional('recurse', V::boolType()),
            V::keyOptional('postal_code', V::stringType()->notEmpty()),
            V::keyOptional('phone_number', V::stringType()->notEmpty()),
            V::keyOptional('street_address', V::stringType()->notEmpty()),

            V::key('access_token', V::stringType()->notEmpty())
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
            V::keyOptional('given_name', V::stringType()->notEmpty()),
            V::keyOptional('middle_name', V::stringType()->notEmpty()),
            V::keyOptional('surname', V::stringType()->notEmpty()),
            V::keyOptional('birthday', V::date('c')),
            V::keyOptional('suffix', V::stringType()->notEmpty()),
            V::keyOptional('nickname', V::stringType()->notEmpty()),
            V::keyOptional('company_name', V::stringType()->notEmpty()),
            V::keyOptional('job_title', V::stringType()->notEmpty()),

            V::keyOptional('manager_name', V::stringType()->notEmpty()),
            V::keyOptional('office_location', V::stringType()->notEmpty()),
            V::keyOptional('notes', V::stringType()->notEmpty()),
            V::keyOptional('emails', V::arrayVal()->each(V::email())),

            V::keyOptional('im_addresses', V::arrayType()),
            V::keyOptional('physical_addresses', V::arrayType()),
            V::keyOptional('phone_numbers', V::arrayType()),
            V::keyOptional('web_pages', V::arrayType()),

            V::key('access_token', V::stringType()->notEmpty())
        ];
    }

    // ------------------------------------------------------------------------------

}
