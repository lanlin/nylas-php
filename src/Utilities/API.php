<?php namespace Nylas\Utilities;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul API List
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/22
 */
class API
{

    // ------------------------------------------------------------------------------

    /**
     * nylas api list array
     */
    const LIST =
    [
        'server' => 'https://api.nylas.com',

        // Authentication
        'oAuthToken'        => '/oauth/token',
        'oAuthRevoke'       => '/oauth/revoke',
        'oAuthAuthorize'    => '/oauth/authorize',
        'connectToken'      => '/connect/token',
        'connectAuthorize'  => '/connect/authorize',

        // Accounts
        'account'            => '/account',
        'listAnAccount'      => '/a/%s/accounts/%s',
        'listAllAccounts'    => '/a/%s/accounts',
        'cancelAnAccount'    => '/a/%s/accounts/%s/downgrade',
        'ReactiveAnAccount'  => '/a/%s/accounts/%s/upgrade',

        // Threads
        'threads'    => '/threads',
        'oneThread'  => '/threads/%s',

        // Messages
        'messages'    => '/messages',
        'oneMessage'  => '/messages/%s',

        // Folders
        'folders'     => '/folders',
        'oneFolder'   => '/folders/%s',

        // Labels
        'labels'      => '/labels',
        'oneLabel'    => '/labels/%s',

        // Drafts
        'drafts'      => '/drafts',
        'oneDraft'    => '/drafts/%s',

        // Sending
        'sending'     => '/send',

        // Files
        'files'         => '/files',
        'oneFile'       => '/files/%s',
        'downloadFile'  => '/files/%s/download',

        // Calendars
        'calendars'     => '/calendars',
        'oneCalendar'   => '/calendars/%s',

        // Events
        'events'       => '/events',
        'oneEvent'     => '/events/%s',
        'RSVPing'      => '/send-rsvp',

        // Contacts
        'contacts'       => '/contacts',
        'oneContact'     => '/contacts/%s',
        'contactPic'     => '/contacts/%s/picture',
        'contactsGroups' => '/contacts/groups',

        // Search
        'searchThreads'  => '/threads/search',
        'searchMessages' => '/messages/search',

        // Webhooks
        'webhooks'    => '/webhooks',
        'oneWebhook'  => '/webhooks/%s',

        // Deltas
        'delta'            => '/delta',
        'deltaLongpoll'    => '/delta/longpoll',
        'deltaStreaming'   => '/delta/streaming',
    ];


    // ------------------------------------------------------------------------------

}
