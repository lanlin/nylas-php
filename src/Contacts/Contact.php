<?php

namespace Nylas\Contacts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Contacts
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#contact-limitations
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Contact
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     *
     * @return array
     */
    public function getContactsList(array $params = []): array
    {
        V::doValidate($this->getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add contact
     *
     * @param array $params
     *
     * @return array
     */
    public function addContact(array $params): array
    {
        V::doValidate(V::keySet(...$this->addContactRules()), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update contact
     *
     * @param array $params
     *
     * @return array
     */
    public function updateContact(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            ...$this->addContactRules(),
        ), $params);

        $path = $params['id'];

        unset($params['id']);

        return $this->options
            ->getSync()
            ->setPath($path)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact
     *
     * @param mixed $contactId string|string[]
     *
     * @return array
     */
    public function getContact(mixed $contactId): array
    {
        $contactId = Helper::fooToArray($contactId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $contactId);

        $queues = [];
        $target = API::LIST['oneContact'];

        foreach ($contactId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request, $target)
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
     * @param mixed $contactId string|string[]
     *
     * @return array
     */
    public function deleteContact(mixed $contactId): array
    {
        $contactId = Helper::fooToArray($contactId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $contactId);

        $queues = [];
        $target = API::LIST['oneContact'];

        foreach ($contactId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request, $target)
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
    public function getContactGroups(): array
    {
        return $this->options
            ->getSync()
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['contactsGroups']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get contact picture file (support multiple download)
     *
     * @param array $params
     *
     * @return array
     */
    public function getContactPicture(array $params): array
    {
        $downloadArr = Helper::arrayToMulti($params);

        V::doValidate($this->pictureRules(), $downloadArr);

        $method = [];
        $target = API::LIST['contactPic'];

        foreach ($downloadArr as $item)
        {
            $sink = $item['path'];

            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $method[] = static function () use ($request, $target, $sink)
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
     * @return \Nylas\Utilities\Validator
     */
    private function pictureRules(): V
    {
        $path = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        return  V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('path', $path)
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function getBaseRules(): V
    {
        return V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
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
    private function addContactRules(): array
    {
        return
        [
            V::keyOptional('given_name', V::stringType()->notEmpty()),
            V::keyOptional('middle_name', V::stringType()->notEmpty()),
            V::keyOptional('surname', V::stringType()->notEmpty()),
            V::keyOptional('birthday', V::date('Y-m-d')),
            V::keyOptional('suffix', V::stringType()->notEmpty()),
            V::keyOptional('nickname', V::stringType()->notEmpty()),

            V::keyOptional('company_name', V::stringType()->notEmpty()),
            V::keyOptional('job_title', V::stringType()->notEmpty()),
            V::keyOptional('manager_name', V::stringType()->notEmpty()),
            V::keyOptional('office_location', V::stringType()->notEmpty()),
            V::keyOptional('notes', V::stringType()->notEmpty()),
            V::keyOptional('group', V::stringType()->notEmpty()),

            $this->contactEmailsRules(),            // emails
            $this->contactWebPageRules(),           // web_pages
            $this->contactImAddressRules(),         // im_addresses
            $this->contactPhoneNumberRules(),       // phone_numbers
            $this->contactPhysicalAddressRules(),   // physical_addresses
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * emails rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function contactEmailsRules(): V
    {
        return V::keyOptional('emails', V::simpleArray(V::keySet(
            V::key('type', V::in(['work', 'personal'])),
            V::key('email', V::stringType()->notEmpty())   // a free-form string
        )));
    }

    // ------------------------------------------------------------------------------

    /**
     * emails rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function contactWebPageRules(): V
    {
        $types = ['profile', 'blog', 'homepage', 'work'];

        return V::keyOptional('web_pages', V::simpleArray(V::keySet(
            V::key('type', V::in($types)),
            V::key('url', V::stringType()->notEmpty())   // a free-form string
        )));
    }

    // ------------------------------------------------------------------------------

    /**
     * im addresses rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function contactImAddressRules(): V
    {
        $types =
        [
            'gtalk', 'aim', 'yahoo', 'lync',
            'skype', 'qq', 'msn', 'icq', 'jabber',
        ];

        return V::keyOptional('im_addresses', V::simpleArray(V::keySet(
            V::key('type', V::in($types)),
            V::key('im_address', V::stringType()->notEmpty())  // a free-form string
        )));
    }

    // ------------------------------------------------------------------------------

    /**
     * phone number rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function contactPhoneNumberRules(): V
    {
        $types =
        [
            'business', 'home', 'mobile', 'pager', 'business_fax',
            'home_fax', 'organization_main', 'assistant', 'radio', 'other',
        ];

        return V::keyOptional('phone_numbers', V::simpleArray(V::keySet(
            V::key('type', V::in($types)),
            V::key('number', V::stringType()->notEmpty()) // a free-form string
        )));
    }

    // ------------------------------------------------------------------------------

    /**
     * physical address rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private function contactPhysicalAddressRules(): V
    {
        return V::keyOptional('physical_addresses', V::simpleArray(V::keySet(
            V::key('type', V::in(['work', 'home', 'other'])),
            V::key('city', V::stringType()->notEmpty()),
            V::key('state', V::stringType()->notEmpty()),
            V::key('country', V::stringType()->notEmpty()),
            V::key('postal_code', V::stringType()->notEmpty()),
            V::key('street_address', V::stringType()->notEmpty())
        )));
    }

    // ------------------------------------------------------------------------------
}
