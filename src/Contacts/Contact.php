<?php

namespace Nylas\Contacts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Contacts
 * ----------------------------------------------------------------------------------
 *
 * @see https://developer.nylas.com/docs/api/#contact-limitations
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
     * Returns all contacts.
     *
     * @see https://developer.nylas.com/docs/api/#get/contacts
     *
     * @param array $params
     *
     * @return array
     */
    public function returnAllContacts(array $params = []): array
    {
        V::doValidate(Validation::getBaseRules(), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates a contact.
     *
     * @see https://developer.nylas.com/docs/api/#post/contacts
     *
     * @param array $params
     *
     * @return array
     */
    public function createAContact(array $params): array
    {
        V::doValidate(Validation::addContactRules(), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['contacts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns a contact by ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/contacts/id
     *
     * @param mixed $contactId string|string[]
     *
     * @return array
     */
    public function returnAContact(mixed $contactId): array
    {
        $contactId = Helper::fooToArray($contactId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $contactId);

        $queues = [];

        foreach ($contactId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneContact']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($contactId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates a contact.
     *
     * @see https://developer.nylas.com/docs/api/#put/contacts/id
     *
     * @param string $contactId
     * @param array  $params
     *
     * @return array
     */
    public function updateAContact(string $contactId, array $params): array
    {
        V::doValidate(Validation::addContactRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $contactId);

        return $this->options
            ->getSync()
            ->setPath($contactId)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneContact']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes a contact.
     *
     * @see https://developer.nylas.com/docs/api/#delete/contacts/id
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

        foreach ($contactId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneContact']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($contactId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Download contact picture file (support multiple download)
     *
     * @see https://developer.nylas.com/docs/api/#get/contacts/id/picture
     *
     * @param array $params
     *
     * @return array
     */
    public function returnsAContactsPicture(array $params): array
    {
        $downloadArr = Helper::arrayToMulti($params);

        V::doValidate(Validation::pictureRules(), $downloadArr);

        $queues = [];

        foreach ($downloadArr as $item)
        {
            $haders = $this->options->getAuthorizationHeader();

            if (!empty($item['auth']))
            {
                $haders['authorization'] = $item['auth'];
            }

            $request = $this->options
                ->getAsync()
                ->setPath($item['id'])
                ->setHeaderParams($haders);

            $queues[] = static function () use ($request, $item)
            {
                return $request->getSink(API::LIST['contactPic'], $item['path']);
            };
        }

        return $this->options->getAsync()->pool($queues, true);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns contact groups. Contact groups provide a way for users to organize their contacts.
     *
     * @see https://developer.nylas.com/docs/api/#get/contacts/groups
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
}
