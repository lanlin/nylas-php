<?php

declare(strict_types = 1);

namespace Nylas\Schedulers;

use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Scheduler Validation
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2023/07/24
 */
class Validation
{
    // ------------------------------------------------------------------------------

    /**
     * get base rules
     *
     * @return V
     */
    public static function getBaseRules(): V
    {
        return V::keySet(
            V::keyOptional('name', V::stringType()::notEmpty()),
            V::keyOptional('slug', V::stringType()::notEmpty()),
            V::keyOptional('access_tokens', V::simpleArray(V::stringType()::notEmpty())),
            V::key('config', V::keySet(
                V::key('event', self::getEventsRules()->setName('event')),
                V::key('booking', self::getBookingRules()->setName('booking')),
                V::key('timezone', V::stringType()::notEmpty()->setName('timezone')),
                V::key('reminders', self::getRemindersRules()->setName('reminders')),
                V::key('appearance', self::getAppearanceRules()->setName('appearance')),
                V::key('calendar_ids', self::getCalendarIdsRules()->setName('calendar_ids')),
                V::keyOptional('locale', V::languageCode()->setName('locale')),
                V::keyOptional('expire_after', self::getExpireAfterRules()->setName('expire_after')),
                V::keyOptional('disable_emails', V::boolType()->setName('disable_emails')),
                V::keyOptional('locale_for_guests', V::languageCode()->setName('locale_for_guests')),
            ))
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private static function getExpireAfterRules(): V
    {
        return V::keySet(
            V::keyOptional('uses', V::intType()::min(-1)),
            V::keyOptional('date', V::oneOf(V::equals(-1), V::timestampType())),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private static function getAppearanceRules(): V
    {
        return V::keySet(
            V::key('show_nylas_branding', V::boolType()),
            V::keyOptional('logo', V::url()),
            V::keyOptional('color', V::stringType()),
            V::keyOptional('submit_text', V::stringType()),
            V::keyOptional('company_name', V::stringType()),
            V::keyOptional('privacy_policy_redirect', V::url()),
            V::keyOptional('thank_you_text', V::stringType()),
            V::keyOptional('thank_you_redirect', V::url()),
            V::keyOptional('thank_you_text_secondary', V::stringType()),
            V::keyOptional('show_week_view', V::boolType()),
            V::keyOptional('show_autoschedule', V::boolType()),
            V::keyOptional('show_timezone_options', V::boolType()),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private static function getCalendarIdsRules(): V
    {
        return V::allOf(
            V::call('array_keys', V::each(V::stringType()::notEmpty())),
            V::each(V::keySet(
                V::key('booking', V::regex('/[a-z0-9]{20,26}/')),
                V::key('availability', V::simpleArray(V::regex('/[a-z0-9]{20,26}/'))),
            ))
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private static function getRemindersRules(): V
    {
        return V::simpleArray(V::keySet(
            V::key('delivery_method', V::in(['email', 'webhook'])),
            V::key('time_before_event', V::intType()),
            V::key('delivery_recipient', V::in(['customer', 'owner', 'both'])),
            V::keyOptional('webhook_url', V::url()),
            V::keyOptional('email_subject', V::stringType()),
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private static function getEventsRules(): V
    {
        return V::keySet(
            V::key('title', V::stringType()::notEmpty()),
            V::key('duration', V::intType()),
            V::key('location', V::stringType()::notEmpty()),
            V::keyOptional('capacity', V::intType()),
            V::keyOptional('template_title', V::stringType()),
            V::keyOptional('participants', V::simpleArray(V::keySet(
                V::key('email', V::email()),
                V::keyOptional('name', V::stringType()),
                V::keyOptional('rsvp', V::stringType()),
                V::keyOptional('role', V::in(['organizer', 'attendee', 'invitee', 'host', 'resource'])),
                V::keyOptional('status', V::in(['no', 'yes', 'maybe', 'noreply'])),
                V::keyOptional('guests', V::simpleArray(V::stringType()::notEmpty())),
                V::keyOptional('comment', V::stringType()),
                V::keyOptional('phone_number', V::phone()),
                V::keyOptional('confirmation_method', V::in(['calendar', 'token'])),
            ))),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return V
     */
    private static function getBookingRules(): V
    {
        $openingHours = V::simpleArray(V::keySet(
            V::key('end', V::regex('/^[0-9]{1,2}:[0-9]{2}$/')),
            V::key('days', V::simpleArray(V::in(['M', 'T', 'W', 'R', 'F', 'S', 'U']))),
            V::key('start', V::regex('/^[0-9]{1,2}:[0-9]{2}$/')),
            V::keyOptional('account_id', V::regex('/[a-z0-9]{20,26}/')),
        ));

        $additionalFields = V::simpleArray(V::keySet(
            V::key('type', V::in(['text', 'multi-line text', 'email', 'phone', 'number', 'dropdown', 'checkbox', 'multi-select list', 'date'])),
            V::key('name', V::stringType()::notEmpty()),
            V::key('label', V::stringType()::notEmpty()),
            V::key('required', V::boolType()),
            V::keyOptional('order', V::number()),
            V::keyOptional('pattern', V::stringType()),
            V::keyOptional('dropdown_options', V::simpleArray(V::stringType()::notEmpty())),
            V::keyOptional('multi_select_options', V::simpleArray(V::stringType()::notEmpty())),
        ));

        return V::keySet(
            V::key('min_buffer', V::intType()),
            V::key('min_booking_notice', V::intType()),
            V::key('min_cancellation_notice', V::intType()),
            V::key('opening_hours', $openingHours),
            V::key('additional_fields', $additionalFields),
            V::key('scheduling_method', V::in(['round-robin-maximize-fairness', 'round-robin-maximize-availability'])),
            V::key('confirmation_method', V::in(['automatic', 'manual', 'external'])),
            V::key('available_days_in_future', V::intType()),
            V::keyOptional('interval_minutes', V::intType()),
            V::keyOptional('name_field_hidden', V::boolType()),
            V::keyOptional('cancellation_policy', V::stringType()),
            V::keyOptional('additional_guests_hidden', V::boolType()),
            V::keyOptional('calendar_invite_to_guests', V::boolType()),
            V::keyOptional('confirmation_emails_to_host', V::boolType()),
            V::keyOptional('confirmation_emails_to_guests', V::boolType()),
        );
    }

    // ------------------------------------------------------------------------------
}
