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
     * get event base rules
     *
     * @return V
     */
    public static function getEventRules(): V
    {
        return V::keySet(
            V::key('when', self::whenRules()),
            V::key('calendar_id', V::stringType()->notEmpty()),
            V::keyOptional('busy', V::boolType()),
            V::keyOptional('title', V::stringType()->notEmpty()),
            V::keyOptional('read_only', V::boolType()),
            V::keyOptional('location', V::stringType()->notEmpty()),
            V::keyOptional('metadata', V::arrayType()),
            V::keyOptional('recurrence', self::recurrenceRules()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('conferencing', self::conferenceRules()),
            V::keyOptional('participants', self::participantsRules()),
            V::keyOptional('notifications', self::notificationRules()),
            V::keyOptional('reminder_method', V::in(['email', 'popup', 'display', 'sound'])),
            V::keyOptional('reminder_minutes', V::regex('\[(|-1|[0-9]{1,})\]')),
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * event filter validate rules
     *
     * @return array
     */
    public static function getFilterRules(): array
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
     * get event when rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function whenRules(): V
    {
        return V::anyOf(
            // time
            V::keySet(V::keyOptional('time', V::timestampType())),

            // date
            V::keySet(V::keyOptional('date', V::date('Y-m-d'))),

            // date span
            V::keySet(
                V::keyOptional('end_date', V::date('Y-m-d')),
                V::keyOptional('start_date', V::date('Y-m-d'))
            ),

            // timespan
            V::keySet(
                V::keyOptional('end_time', V::timestampType()),
                V::keyOptional('start_time', V::timestampType()),
                V::keyOptional('end_timezone', V::in(DateTimeZone::listIdentifiers())),
                V::keyOptional('start_timezone', V::in(DateTimeZone::listIdentifiers()))
            ),
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
        return V::each(V::anyOf(
            V::keySet(
                V::keyOptional('type', V::equals('sms')),
                V::keyOptional('message', V::stringType()->notEmpty()),
                V::keyOptional('minutes_before_events', V::intType()),
            ),
            V::keySet(
                V::keyOptional('type', V::equals('email')),
                V::keyOptional('body', V::stringType()->notEmpty()),
                V::keyOptional('subject', V::stringType()->notEmpty()),
                V::keyOptional('minutes_before_events', V::intType()),
            ),
            V::keySet(
                V::keyOptional('url', V::url()),
                V::keyOptional('type', V::equals('webhook')),
                V::keyOptional('payload', V::stringType()->notEmpty()),
                V::keyOptional('minutes_before_events', V::intType()),
            ),
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
        $autocreate = V::keySet(
            V::key('provider', V::in(['Google Meet', 'Zoom Meeting'])),
            V::key('autocreate', V::arrayType()),
        );

        $webEx = V::keySet(
            V::key('provider', V::equals('WebEx')),
            V::key('details', V::keySet(
                V::keyOptional('password', V::stringType()),
                V::keyOptional('pin', V::stringType()),
                V::keyOptional('url', V::stringType())
            ))
        );

        $zoomMeeting = V::keySet(
            V::key('provider', V::equals('Zoom Meeting')),
            V::key('details', V::keySet(
                V::keyOptional('meeting_code', V::stringType()),
                V::keyOptional('password', V::stringType()),
                V::keyOptional('url', V::stringType()),
            ))
        );

        $goToMeeting = V::keySet(
            V::key('provider', V::equals('GoToMeeting')),
            V::key('details', V::keySet(
                V::keyOptional('meeting_code', V::stringType()),
                V::keyOptional('phone', V::simpleArray()),
                V::keyOptional('url', V::stringType()),
            ))
        );

        $googleMeet = V::keySet(
            V::key('provider', V::equals('Google Meet')),
            V::key('details', V::keySet(
                V::keyOptional('phone', V::simpleArray()),
                V::keyOptional('pin', V::stringType()),
                V::keyOptional('url', V::stringType()),
            ))
        );

        return V::anyOf($autocreate, $webEx, $zoomMeeting, $goToMeeting, $googleMeet);
    }

    // ------------------------------------------------------------------------------
}
