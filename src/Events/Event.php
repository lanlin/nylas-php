<?php

namespace Nylas\Events;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Events
 * ----------------------------------------------------------------------------------
 *
 * @see https://docs.nylas.com/reference#event-limitations
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Event
{
    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    /**
     * @var string
     */
    private string $notify = 'notify_participants';

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
     *
     * @return array
     */
    public function returnAllEvents(array $params = []): array
    {
        V::doValidate(V::keySet(
            V::keyOptional('view', V::in(['ids', 'count'])),
            ...Validation::getFilterRules()
        ), $params);

        return $this->options
            ->getSync()
            ->setQuery($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->get(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Creates an event, conference or add metadata.
     *
     * @see https://developer.nylas.com/docs/api/#post/events
     *
     * @param array $params
     * @param bool  $notifyParticipants
     *
     * @return array
     */
    public function createAnEvent(array $params, ?bool $notifyParticipants = null): array
    {
        V::doValidate(Validation::getEventRules(), $params);

        $query = \is_null($notifyParticipants) ? [] : [$this->notify => $notifyParticipants];

        return $this->options
            ->getSync()
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['events']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Returns an event by ID.
     *
     * @see https://developer.nylas.com/docs/api/#get/events/id
     *
     * @param mixed $eventId
     *
     * @return array
     */
    public function returnAnEvent(mixed $eventId): array
    {
        $eventId = Helper::fooToArray($eventId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $eventId);

        $queues = [];

        foreach ($eventId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->get(API::LIST['oneEvent']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($eventId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * Updates an event, conference, or metadata.
     *
     * @see https://developer.nylas.com/docs/api/#put/events/id
     *
     * @param string $eventId
     * @param array  $params
     * @param bool   $notifyParticipants
     *
     * @return array
     */
    public function updateAnEvent(string $eventId, array $params, ?bool $notifyParticipants = null): array
    {
        V::doValidate(Validation::getEventRules(), $params);
        V::doValidate(V::stringType()->notEmpty(), $eventId);

        $query = \is_null($notifyParticipants) ? [] : [$this->notify => $notifyParticipants];

        return $this->options
            ->getSync()
            ->setPath($eventId)
            ->setQuery($query)
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->put(API::LIST['oneEvent']);
    }

    // ------------------------------------------------------------------------------

    /**
     * Deletes an event.
     *
     * @see https://developer.nylas.com/docs/api/#delete/events/id
     *
     * @param string $eventId
     * @param bool   $notifyParticipants
     *
     * @return array
     */
    public function deleteAnEvent(mixed $eventId, ?bool $notifyParticipants = null): array
    {
        $eventId = Helper::fooToArray($eventId);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $eventId);

        $queues = [];
        $query  = \is_null($notifyParticipants) ? [] : [$this->notify => $notifyParticipants];

        foreach ($eventId as $id)
        {
            $request = $this->options
                ->getAsync()
                ->setPath($id)
                ->setQuery($query)
                ->setHeaderParams($this->options->getAuthorizationHeader());

            $queues[] = static function () use ($request)
            {
                return $request->delete(API::LIST['oneEvent']);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($eventId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * The RSVP endpoint allows you to send attendance status updates to event organizers.
     *
     * @see https://developer.nylas.com/docs/api/#post/send-rsvp
     *
     * @param array $params
     *
     * @return array
     */
    public function sendRSVP(array $params): array
    {
        V::doValidate(V::keySet(
            V::key('status', V::in(['yes', 'no', 'maybe'])),
            V::key('event_id', V::stringType()->notEmpty()),
            V::key('account_id', V::stringType()->notEmpty()),
        ), $params);

        return $this->options
            ->getSync()
            ->setFormParams($params)
            ->setHeaderParams($this->options->getAuthorizationHeader())
            ->post(API::LIST['rsvpEvent']);
    }

    // ------------------------------------------------------------------------------
}
