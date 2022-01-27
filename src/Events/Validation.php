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
 * @change 2022/01/27
 */
class Validation
{
    // ------------------------------------------------------------------------------

    /**
     * get ics file rules
     *
     * @return \Nylas\Utilities\Validator
     */
    public static function getICSRules(): V
    {
        return V::anyOf(
            V::keySet(
                V::key('event_id', V::stringType()->notEmpty()),
                V::key('ics_options', V::keySet(
                    V::key('method', V::in(['request', 'publish', 'reply', 'add', 'cancel', 'refresh'])),
                    V::key('prodid', V::stringType()->notEmpty()),
                    V::key('ical_uid', V::stringType()->notEmpty()),
                )),
            ),
            self::getEventRules(),
        );
    }

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
            V::keyOptional('metadata', self::metadataRules()),
            V::keyOptional('recurrence', self::recurrenceRules()),
            V::keyOptional('description', V::stringType()->notEmpty()),
            V::keyOptional('conferencing', self::conferenceRules()),
            V::keyOptional('participants', self::participantsRules()),
            V::keyOptional('notifications', self::notificationRules()),
            V::keyOptional('reminder_method', V::in(['email', 'popup', 'display', 'sound'])),
            V::keyOptional('reminder_minutes', V::regex('#\[(|-1|[0-9]{1,})\]#')),
            V::keyOptional('round_robin_order', V::simpleArray(V::email()))
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

            // @see https://developer.nylas.com/docs/developer-tools/api/metadata/#keep-in-mind
            V::keyOptional('metadata_key', V::stringType()->length(1, 40)),
            V::keyOptional('metadata_value', V::stringType()->length(1, 500)),
            V::keyOptional('metadata_paire', V::stringType()->length(3, 27100)),
            V::keyOptional('metadata_search', V::stringType()->notEmpty()),

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
            V::keyOptional('comment', V::anyOf(V::nullType(), V::stringType())),
            V::keyOptional('phone_number', V::anyOf(V::nullType(), V::phone()))
        ));
    }

    // ------------------------------------------------------------------------------

    /**
     * get metadata array rules
     *
     * @see https://developer.nylas.com/docs/developer-tools/api/metadata/#keep-in-mind
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function metadataRules(): V
    {
        return V::callback(static function (mixed $input): bool
        {
            if (!\is_array($input) || \count($input) > 50)
            {
                return false;
            }

            $keys = \array_keys($input);
            $isOk = V::each(V::stringType()->length(1, 40))->validate($keys);

            if (!$isOk)
            {
                return false;
            }

            // https://developer.nylas.com/docs/developer-tools/api/metadata/#delete-metadata
            return V::each(V::stringType()->length(0, 500))->validate(\array_values($input));
        });
    }

    // ------------------------------------------------------------------------------

    /**
     * get event when rules
     *
     * @return \Nylas\Utilities\Validator
     */
    private static function whenRules(): V
    {
        // https://en.wikipedia.org/wiki/ISO_8601#Calendar_dates
        $dates = V::anyOf(V::date('Y-m'), V::date('Ymd'), V::date('Y-m-d'));

        return V::anyOf(
            // date
            V::keySet(V::keyOptional('date', $dates)),
            // date span
            V::keySet(
                V::keyOptional('end_date', $dates),
                V::keyOptional('start_date', $dates)
            ),
            // time
            V::keySet(
                V::keyOptional('time', V::timestampType()),
                V::keyOptional('timezone', V::in(DateTimeZone::listIdentifiers()))
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
                V::keyOptional('minutes_before_events', V::regex('#\d+#'))
            ),
            V::keySet(
                V::keyOptional('type', V::equals('email')),
                V::keyOptional('body', V::stringType()->notEmpty()),
                V::keyOptional('subject', V::stringType()->notEmpty()),
                V::keyOptional('minutes_before_events', V::regex('#\d+#'))
            ),
            V::keySet(
                V::keyOptional('url', V::url()),
                V::keyOptional('type', V::equals('webhook')),
                V::keyOptional('payload', V::stringType()->notEmpty()),
                V::keyOptional('minutes_before_events', V::regex('#\d+#'))
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
            V::key('provider', V::in(['Google Meet', 'Zoom Meeting', 'Microsoft Teams'])),
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
