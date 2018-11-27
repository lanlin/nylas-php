<?php namespace Nylas\Events;

use Nylas\Utilities\API;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Events
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/23
 */
class Event
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Event constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get events list
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getEventsList(array $params)
    {
        $rules = $this->getBaseRules();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getRequest()
        ->setQuery($params)
        ->setHeaderParams($header)
        ->get(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get event
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function getEvent(array $params)
    {
        $temps = [V::key('id', V::stringType()::notEmpty())];
        $rules = array_merge($temps, $this->getBaseRules());

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->get(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add event
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function addEvent(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        if (!$this->addEventRules()->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];
        $query  = ['notify_participants' => $params['notify_participants']];

        unset($params['access_token'], $params['notify_participants']);

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update event
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function updateEvent(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        if (!$this->updateEventRules()->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];
        $query  = ['notify_participants' => $params['notify_participants']];

        unset($params['id'], $params['access_token'], $params['notify_participants']);

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setQuery($query)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete event
     *
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function deleteEvent(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('notify_participants', V::boolType(), false)
        );

        if (!$rule->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        $temps = empty($params['notify_participants']);
        $query = $temps ? [] : ['notify_participants' => $params['notify_participants']];

        return $this->options
        ->getRequest()
        ->setPath($path)
        ->setQuery($query)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * @param array $params
     * @return mixed
     * @throws \Nylas\Exceptions\NylasException
     */
    public function rsvping(array $params)
    {
        $params['account_id'] =
        $params['account_id'] ?? $this->options->getAccountId();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rules = V::keySet(
            V::key('status', V::in(['yes', 'no', 'maybe'])),
            V::key('event_id', V::stringType()::notEmpty()),
            V::key('account_id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),
            V::key('notify_participants', V::boolType(), false)
        );

        if (!$rules->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $temps = empty($params['notify_participants']);
        $query = $temps ? [] : ['notify_participants' => $params['notify_participants']];

        $header = ['Authorization' => $params['access_token']];
        unset($params['access_token'], $params['notify_participants']);

        return $this->options
        ->getRequest()
        ->setQuery($query)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->delete(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * event base validate rules
     *
     * @return array
     */
    private function getBaseRules()
    {
        return
        [
            V::key('limit', V::intType()::min(1), false),
            V::key('offset', V::intType()::min(0), false),
            V::key('event_id', V::stringType()::notEmpty(), false),
            V::key('calendar_id', V::stringType()::notEmpty(), false),

            V::key('title', V::stringType()::notEmpty(), false),
            V::key('location', V::stringType()::notEmpty(), false),
            V::key('description', V::stringType()::notEmpty(), false),
            V::key('show_cancelled', V::boolType(), false),
            V::key('expand_recurring', V::boolType(), false),

            V::key('ends_after', V::timestampType(), false),
            V::key('ends_before', V::timestampType(), false),
            V::key('start_after', V::timestampType(), false),
            V::key('start_before', V::timestampType(), false),

            V::key('access_token', V::stringType()::notEmpty())
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update event
     *
     * @return \Respect\Validation\Validator
     */
    private function updateEventRules()
    {
        return V::keySet(
            V::key('id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),

            V::key('when', $this->timeRules(), false),
            V::key('busy', V::boolType(), false),
            V::key('title', V::stringType()::notEmpty(), false),
            V::key('location', V::stringType()::notEmpty(), false),
            V::key('description', V::stringType()::notEmpty(), false),
            V::key('notify_participants', V::boolType(), false),

            V::key('participants', V::arrayVal()->each(V::keySet(
                V::key('email', V::email()),
                V::key('status', V::stringType()),
                V::key('name', V::stringType()),
                V::key('comment', V::stringType())
            )), false)
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for add event
     *
     * @return \Respect\Validation\Validator
     */
    private function addEventRules()
    {
        return V::keySet(
            V::key('when', $this->timeRules()),
            V::key('calendar_id', V::stringType()::notEmpty()),
            V::key('access_token', V::stringType()::notEmpty()),

            V::key('busy', V::boolType(), false),
            V::key('title', V::stringType()::notEmpty(), false),
            V::key('location', V::stringType()::notEmpty(), false),
            V::key('recurrence', V::arrayType(), false),
            V::key('description', V::stringType()::notEmpty(), false),
            V::key('notify_participants', V::boolType(), false),

            V::key('participants', V::arrayVal()->each(V::keySet(
                V::key('email', V::email()),
                V::key('status', V::stringType()),
                V::key('name', V::stringType()),
                V::key('comment', V::stringType())
            )), false)
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * get event time rules
     *
     * @return \Respect\Validation\Validator
     */
    private function timeRules()
    {
        return V::oneOf(

            // time
            V::keySet(V::key('time', V::timestampType())),

            // date
            V::keySet(V::key('date', V::date('c'))),

            // timespan
            V::keySet(
                V::key('end_time', V::timestampType()),
                V::key('start_time', V::timestampType())
            ),

            // date span
            V::keySet(
                V::key('end_date', V::date('c')),
                V::key('start_date', V::date('c'))
            )
        );
    }

    // ------------------------------------------------------------------------------

}
