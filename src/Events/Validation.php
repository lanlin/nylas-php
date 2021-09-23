<?php

namespace Nylas\Events;

use DateTimeZone;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Events Validation
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#event-limitations
 *
 * @author lanlin
 * @change 2021/09/23
 */
class Validation
{
    // ------------------------------------------------------------------------------

    /**
     * rules for add event
     *
     * @return \Nylas\Utilities\Validator
     */
    public static function addEventRules(): V
    {
        return V::keySet(
            V::key('when', self::timeRules()),
            ...self::getEventBaseRules(),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update event
     *
     * @return \Nylas\Utilities\Validator
     */
    public static function updateEventRules(): V
    {
        return V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::keyOptional('when', self::timeRules()),
            ...self::getEventBaseRules(),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * event base validate rules
     *
     * @return array
     */
    public static function getBaseRules(): array
    {
        return
        [
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('count', V::intType()),
            V::keyOptional('limit', V::intType()->min(1)),
            V::keyOptional('offset', V::intType()->min(0)),
            V::keyOptional('event_id', V::stringType()->notEmpty()),
            V::keyOptional('calendar_id', V::stringType()->notEmpty()),

            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('show_cancelled', V::boolType()),
            V::keyOptional('expand_recurring', V::boolType()),

            V::keyOptional('metadata_key', V::url()),
            V::keyOptional('metadata_value', V::url()),
            V::keyOptional('metadata_paire', V::url()),

            V::keyOptional('ends_after', V::timestampType()),
            V::keyOptional('ends_before', V::timestampType()),
            V::keyOptional('starts_after', V::timestampType()),
            V::keyOptional('starts_before', V::timestampType()),
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Nylas\Utilities\Validator
     */
    private static function recurrenceRules(): V
    {
        return V::keySet(
            V::key('rrule', V::simpleArray(V::stringType()->notEmpty())),
            V::key('timezone', V::in(DateTimeZone::listIdentifiers())),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * @return \Nylas\Utilities\Validator
     */
    private static function participantsRules(): V
    {
        return V::simpleArray(V::keySet(
            V::key('email', V::email()),
            V::keyOptional('name', V::anyOf(V::nullType(), V::stringType())),
            V::keyOptional('status', V::in(['yes', 'no', 'maybe', 'noreply'])),
            V::keyOptional('comment', V::anyOf(V::nullType(), V::stringType()))
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * get event base rules
     *
     * @return array
     */
    private static function getEventBaseRules(): array
    {
        return
        [
            V::key('calendar_id', V::stringType()->notEmpty()),
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('read_only', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('metadata', V::arrayType()),
            V::keyOptional('recurrence', self::recurrenceRules()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('conferencing', self::conferenceRules()),
            V::keyOptional('participants', self::participantsRules()),
            V::keyOptional('notifications', self::notificationRules()),
            V::keyOptional('reminder_method', V::in(['email', 'popup', 'display', 'sound'])),
            V::keyOptional('reminder_minutes', V::regex('\[(|-1|[0-9]{1,})\]')),
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * get event time rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function timeRules(): V
    {
        return V::anyOf(
            // time
            V::keySet(V::key('time', V::timestampType())),

            // date
            V::keySet(V::key('date', V::date('Y-m-d'))),

            // timespan
            V::keySet(
                V::key('end_time', V::timestampType()),
                V::key('start_time', V::timestampType())
            ),

            // date span
            V::keySet(
                V::key('end_date', V::date('Y-m-d')),
                V::key('start_date', V::date('Y-m-d'))
            )
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get event notification rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function notificationRules(): V
    {
        return V::each(V::oneOf(
            V::keySet(V::key('sms', V::keySet(
                V::key('type', V::equals('sms')),
                V::key('message', V::stringType()->notEmpty()),
                V::key('minutes_before_events', V::intType()),
            ))),
            V::keySet(V::key('email', V::keySet(
                V::key('type', V::equals('email')),
                V::key('body', V::stringType()->notEmpty()),
                V::key('subject', V::stringType()->notEmpty()),
                V::key('minutes_before_events', V::intType()),
            ))),
            V::keySet(V::key('webhooks', V::keySet(
                V::key('url', V::url()),
                V::key('type', V::equals('webhook')),
                V::key('payload', V::stringType()->notEmpty()),
                V::key('minutes_before_events', V::intType()),
            ))),
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * get event conference rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function conferenceRules(): V
    {
        $webEx = V::keySet(
            V::key('provider', V::equals('WebEx')),
            V::key('details', V::keySet(
                V::key('password', V::stringType()),
                V::key('pin', V::stringType()),
                V::key('url', V::stringType())
            ))
        );

        $zoomMeeting = V::keySet(
            V::key('provider', V::equals('Zoom Meeting')),
            V::key('details', V::keySet(
                V::key('meeting_code', V::stringType()),
                V::key('password', V::stringType()),
                V::key('url', V::stringType()),
            ))
        );

        $goToMeeting = V::keySet(
            V::key('provider', V::equals('GoToMeeting')),
            V::key('details', V::keySet(
                V::key('meeting_code', V::stringType()),
                V::key('phone', V::simpleArray()),
                V::key('url', V::stringType()),
            ))
        );

        $googleMeet = V::keySet(
            V::key('provider', V::equals('Google Meet')),
            V::key('details', V::keySet(
                V::key('phone', V::simpleArray()),
                V::key('pin', V::stringType()),
                V::key('url', V::stringType()),
            ))
        );

        return V::oneOf(
            V::keySet(V::key('details', V::oneOf($webEx, $zoomMeeting, $goToMeeting, $googleMeet))),
            V::keySet(V::key('autocreate', V::keySet(
                V::key('provider', V::in(['Google Meet', 'Zoom Meeting'])),
                V::key('autocreate', V::arrayType()),
            ))),
        );
    }

    // ------------------------------------------------------------------------------
}
