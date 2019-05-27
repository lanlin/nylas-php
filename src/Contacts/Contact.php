<?php namespace Nylas\Contacts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Contacts
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
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
     * @return array
     */
    public function getContactsList(array $params = [])
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->getBaseRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add contact
     *
     * @param array $params
     * @return array
     */
    public function addContact(array $params)
    {
        $rules = $this->addContactRules();

        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update contact
     *
     * @param array $params
     * @return array
     */
    public function updateContact(array $params)
    {
        $rules = $this->addContactRules();

        $accessToken = $this->options->getAccessToken();

        array_push($rules,  V::key('id', V::stringType()->notEmpty()));

        V::doValidate(V::keySet(...$rules), $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $path   = $params['id'];
        $header = ['Authorization' => $accessToken];

        unset($params['id']);

        return $this->options
        ->getSync()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact
     *
     * @param string|array $contactId
     * @return array
     */
    public function getContact($contactId)
    {
        $contactId   = Helper::fooToArray($contactId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $contactId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneContact'];
        $header = ['Authorization' => $accessToken];

        foreach ($contactId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($contactId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete contact
     *
     * @param string|array $contactId
     * @return array
     */
    public function deleteContact($contactId)
    {
        $contactId   = Helper::fooToArray($contactId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $contactId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneContact'];
        $header = ['Authorization' => $accessToken];

        foreach ($contactId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($contactId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact groups
     *
     * @return array
     */
    public function getContactGroups()
    {
        $accessToken = $this->options->getAccessToken();

        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setHeaderParams($header)
        ->get(API::LIST['contactsGroups']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact picture file (support multiple download)
     *
     * @param array $params
     * @return array
     */
    public function getContactPicture(array $params)
    {
        $downloadArr = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        V::doValidate($this->pictureRules(), $downloadArr);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $method = [];
        $target = API::LIST['contactPic'];
        $header = ['Authorization' => $accessToken];

        foreach ($downloadArr as $item)
        {
            $sink = $item['path'];

            $request = $this->options
            ->getAsync()
            ->setPath($item['id'])
            ->setHeaderParams($header);

            $method[] = function () use ($request, $target, $sink)
            {
                return $request->getSink($target, $sink);
            };
        }

        return $this->options->getAsync()->pool($method, true);
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for download picture
     *
     * @return \Respect\Validation\Validator
     */
    private function pictureRules()
    {
        $path = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        return  V::arrayType()->each(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('path', $path)
        ));
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
            V::keyOptional('street_address', V::stringType()->notEmpty())
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
            V::keyOptional('emails', V::arrayVal()->each( v::keySet(v::key('type', v::stringType()->notEmpty()), v::key('email', V::email()->notEmpty())))),
            V::keyOptional('im_addresses', V::arrayType()),
            V::keyOptional('physical_addresses', V::arrayType()),
            V::keyOptional('phone_numbers', V::arrayType()),
            V::keyOptional('web_pages', V::arrayType())
        ];
    }

    // ------------------------------------------------------------------------------

}
