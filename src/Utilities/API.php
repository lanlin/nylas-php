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
 * @version 2.2 (2021/04/30)
 *
 * @author lanlin
 * @change 2021/09/22
 */
class API
{
    // ------------------------------------------------------------------------------

    /**
     * nylas server list array
     *
     * @see https://developer.nylas.com/docs/api/#servers
     */
    public const SERVER = [
        'us'      => 'https://api.nylas.com',
        'canada'  => 'https://canada.api.nylas.com',
        'ireland' => 'https://ireland.api.nylas.com',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas account status
     *
     * @see https://developer.nylas.com/docs/the-basics/manage-accounts/account-sync-status/#account-management
     */
    public const STATUS =
    [
        'running',             // 'Sync is successfully running. No action required. It means we are up to date and listening for new changes.',
        'stopped',             // 'Sync is stopped. This can happen for a variety of reasons. If you are adding accounts beyond the limits of your free trial period, some may be stopped until you upgrade your Nylas application. It can also occur when we repeatedly encounter unexpected errors. These errors most commonly originate from incompatibilities with upstream providers or temporary outages.',
        'exception',           // 'This can occur if an upstream provider returns an error our sync engine does not yet understand. Please contact support@nylas.com for accounts in this state.',
        'sync-error',          // 'This means an unexpected error was raised while syncing an account. Please contact support@nylas.com for accounts in this state.',
        'downloading',         // 'The account is in normal operation but has not yet finished its initial sync.',
        'initializing',        // 'The account is freshly connected with no known problems. This should be the very first momentary state of an account and is part of normal operation.',
        'invalid-credentials', // 'Authenticating failure with mail server. You should prompt the user to re-authorize.',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas providers
     *
     * @see https://developer.nylas.com/docs/developer-tools/api/supported-providers/
     */
    public const PROVIDERS = [
        'gmail',
        'yahoo',
        'exchange',
        'outlook',
        'imap',
        'icloud',
        'hotmail',
        'aol',
        'office365',
        'nylas',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas scopes
     *
     * @see https://developer.nylas.com/docs/the-basics/authentication/authentication-scopes/#nylas-scopes
     */
    public const SCOPES = [
        'email.modify',	            // Read and modify all messages, threads, file attachments, and read email metadata like headers. Does not include send.
        'email.read_only',	        // Read all messages, threads, file attachments, drafts, and email metadata like headers—no write operations.
        'email.send',	            // Send messages only. No read or modify privileges on users' emails. Using email.send as the only scope with Gmail accounts may lead to unexpected threading behavior.
        'email.folders_and_labels',	// Read and modify folders or labels, depending on the account type.
        'email.metadata',	        // Read email metadata including headers and labels/folders, but not the message body or file attachments.
        'email.drafts',	            // Read and modify drafts. Does not include send.
        'calendar',	                // Read and modify calendars and events.
        'calendar.read_only',	    // Read calendars and events.
        'room_resources.read_only',	// Read available room resources for an account. Room resources for Office 365 is an Admin Consent Required permission.
        'contacts',	                // Read and modify contacts.
        'contacts.read_only',	    // Read contacts.
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas api list array
     *
     * @see https://developer.nylas.com/docs/api/#overview
     */
    public const LIST = [
        // Authentication
        'oAuthToken'       => '/oauth/token',
        'oAuthRevoke'      => '/oauth/revoke',
        'oAuthAuthorize'   => '/oauth/authorize',
        'connectToken'     => '/connect/token',
        'connectAuthorize' => '/connect/authorize',

        // management
        'account'           => '/account',
        'manageApp'         => '/a/%s',
        'ipAddresses'       => '/a/%s/ip_addresses',
        'listAllAccounts'   => '/a/%s/accounts',
        'listAnAccount'     => '/a/%s/accounts/%s',
        'cancelAnAccount'   => '/a/%s/accounts/%s/downgrade',
        'revokeAllTokens'   => '/a/%s/accounts/%s/revoke-all',
        'tokenInfo'         => '/a/%s/accounts/%s/token-info',
        'reactiveAnAccount' => '/a/%s/accounts/%s/upgrade',

        // Threads
        'threads'   => '/threads',
        'oneThread' => '/threads/%s',

        // Messages
        'messages'   => '/messages',
        'oneMessage' => '/messages/%s',

        // Folders (new PUT folder)
        'folders'   => '/folders',
        'oneFolder' => '/folders/%s',

        // Labels
        'labels'   => '/labels',
        'oneLabel' => '/labels/%s',

        // Drafts
        'drafts'   => '/drafts',
        'oneDraft' => '/drafts/%s',

        // Sending
        'sending' => '/send',

        // Files
        'files'        => '/files',
        'oneFile'      => '/files/%s',
        'downloadFile' => '/files/%s/download',

        // Calendars
        'calendars'   => '/calendars',
        'oneCalendar' => '/calendars/%s',

        // Events
        'events'   => '/events',
        'oneEvent' => '/events/%s',
        'RSVPing'  => '/send-rsvp',

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
        'delta'             => '/delta',
        'deltaLongpoll'     => '/delta/longpoll',
        'deltaStreaming'    => '/delta/streaming',
        'deltaLatestCursor' => '/delta/latest_cursor',

        // JobStatuses
        'jobStatuses'  => '/job-statuses',
        'oneJobStatus' => '/job-statuses/%s',

    ];

    // ------------------------------------------------------------------------------
}
