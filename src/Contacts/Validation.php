<?php

namespace Nylas\Contacts;

use Nylas\Utilities\Validator as V;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Contacts
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/24
 */
class Validation
{
    // ------------------------------------------------------------------------------

    /**
     * rules for download picture
     *
     * @return \Nylas\Utilities\Validator
     */
    public static function pictureRules(): V
    {
        $path = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        return  V::simpleArray(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('path', $path),
            V::keyOptional('auth', V::stringType()->notEmpty())
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return \Nylas\Utilities\Validator
     */
    public static function getBaseRules(): V
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
     * @return V
     */
    public static function addContactRules(): V
    {
        return V::keySet(
            V::keyOptional('group', V::stringType()->notEmpty()),
            V::keyOptional('notes', V::stringType()->notEmpty()),
            V::keyOptional('job_title', V::stringType()->notEmpty()),
            V::keyOptional('manager_name', V::stringType()->notEmpty()),
            V::keyOptional('office_location', V::stringType()->notEmpty()),

            V::keyOptional('suffix', V::stringType()->notEmpty()),
            V::keyOptional('surname', V::stringType()->notEmpty()),
            V::keyOptional('birthday', V::date('Y-m-d')),
            V::keyOptional('nickname', V::stringType()->notEmpty()),
            V::keyOptional('given_name', V::stringType()->notEmpty()),
            V::keyOptional('middle_name', V::stringType()->notEmpty()),
            V::keyOptional('company_name', V::stringType()->notEmpty()),

            self::contactEmailsRules(),            // emails
            self::contactWebPageRules(),           // web_pages
            self::contactImAddressRules(),         // im_addresses
            self::contactPhoneNumberRules(),       // phone_numbers
            self::contactPhysicalAddressRules(),   // physical_addresses
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * emails rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function contactEmailsRules(): V
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
    private static function contactWebPageRules(): V
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
    private static function contactImAddressRules(): V
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
    private static function contactPhoneNumberRules(): V
    {
        $types =
        [
            'business', 'home', 'mobile', 'page', 'business_fax',
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
    private static function contactPhysicalAddressRules(): V
    {
        return V::keyOptional('physical_addresses', V::simpleArray(V::keySet(
            V::key('type', V::in(['work', 'home', 'other'])),
            V::key('city', V::stringType()->notEmpty()),
            V::key('state', V::stringType()->notEmpty()),
            V::key('format', V::stringType()->notEmpty()),
            V::key('country', V::stringType()->notEmpty()),
            V::key('postal_code', V::stringType()->notEmpty()),
            V::key('street_address', V::stringType()->notEmpty())
        )));
    }

    // ------------------------------------------------------------------------------
}
