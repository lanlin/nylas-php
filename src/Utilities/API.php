<?php

namespace Nylas\Utilities;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul API List
 * ----------------------------------------------------------------------------------
 *
 * @see https://changelog.nylas.com/
 * @see https://docs.nylas.com/reference#api-changelog
 *
 * @version 2.2 (2020/09/30)
 *
 * @author lanlin
 * @change 2021/07/20
 */
class API
{
    // ------------------------------------------------------------------------------

    /**
     * nylas server list array
     */
    public const SERVER = [
        'us'      => 'https://api.nylas.com',
        'canada'  => 'https://canada.api.nylas.com',
        'ireland' => 'https://ireland.api.nylas.com',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas api list array
     */
    public const LIST =
    [
        // Authentication
        'oAuthToken'        => '/oauth/token',
        'oAuthRevoke'       => '/oauth/revoke',
        'oAuthAuthorize'    => '/oauth/authorize',
        'connectToken'      => '/connect/token',
        'connectAuthorize'  => '/connect/authorize',

        // management
        'account'            => '/account',
        'manageApp'          => '/a/%s',
        'ipAddresses'        => '/a/%s/ip_addresses',
        'listAllAccounts'    => '/a/%s/accounts',
        'listAnAccount'      => '/a/%s/accounts/%s',
        'cancelAnAccount'    => '/a/%s/accounts/%s/downgrade',
        'revokeAllTokens'    => '/a/%s/accounts/%s/revoke-all',
        'tokenInfo'          => '/a/%s/accounts/%s/token-info',
        'reactiveAnAccount'  => '/a/%s/accounts/%s/upgrade',

        // Threads
        'threads'    => '/threads',
        'oneThread'  => '/threads/%s',

        // Messages
        'messages'    => '/messages',
        'oneMessage'  => '/messages/%s',

        // Folders (new PUT folder)
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
        'freeBusy'      => '/calendars/free-busy',

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
        'webhooks'   => '/a/%s/webhooks',
        'oneWebhook' => '/a/%s/webhooks/%s',

        // Deltas
        'delta'              => '/delta',
        'deltaLongpoll'      => '/delta/longpoll',
        'deltaStreaming'     => '/delta/streaming',
        'deltaLatestCursor'  => '/delta/latest_cursor',

        // JobStatuses
        'jobStatuses'  => '/job-statuses',
        'oneJobStatus' => '/job-statuses/%s',

    ];

    // ------------------------------------------------------------------------------
}
