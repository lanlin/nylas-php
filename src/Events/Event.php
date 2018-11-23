<?php namespace Nylas\Events;

use Nylas\Utilities\API;
use Nylas\Utilities\Request;
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
     * @var Request
     */
    private $request;

    // ------------------------------------------------------------------------------

    /**
     * Hosted constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
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

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->request
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

        if (!V::keySet(...$rules)->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->request
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
        if (!$this->addEventRules()->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $header = ['Authorization' => $params['access_token']];
        $query  = ['notify_participants' => $params['notify_participants']];

        unset($params['access_token'], $params['notify_participants']);

        return $this->request
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
        if (!$this->updateEventRules()->validate($params))
        {
            throw new NylasException('invalid params');
        }

        $path   = [$params['id']];
        $header = ['Authorization' => $params['access_token']];
        $query  = ['notify_participants' => $params['notify_participants']];

        unset($params['id'], $params['access_token'], $params['notify_participants']);

        return $this->request
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
        $query  = empty($params['notify_participants']) ? [] : ['notify_participants' => $params['notify_participants']];

        return $this->request
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

        $header = ['Authorization' => $params['access_token']];
        $query  = empty($params['notify_participants']) ? [] : ['notify_participants' => $params['notify_participants']];

        unset($params['access_token'], $params['notify_participants']);

        return $this->request
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
