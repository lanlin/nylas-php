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
 * @version 2.3 (2022/01/27)
 *
 * @author lanlin
 * @change 2022/01/27
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
    public const STATUS = [
        'running',             // Sync is successfully running. No action required. It means we are up to date and listening for new changes.
        'stopped',             // Sync is stopped. This can happen for a variety of reasons.
        'exception',           // This can occur if an upstream provider returns an error our sync engine does not yet understand.
        'sync-error',          // This means an unexpected error was raised while syncing an account.
        'downloading',         // The account is in normal operation but has not yet finished its initial sync.
        'initializing',        // The account is freshly connected with no known problems.
        'invalid-credentials', // Authenticating failure with mail server. You should prompt the user to re-authorize.
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas providers
     *
     * @see https://developer.nylas.com/docs/developer-tools/api/supported-providers/
     * @see https://developer.nylas.com/docs/api/#post/connect/authorize
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
        'email',                    // Send and modify all messages, threads, file attachments, and read email metadata like headers
        'email.modify',	            // Read and modify all messages, threads, file attachments, and read email metadata like headers. Does not include send.
        'email.read_only',	        // Read all messages, threads, file attachments, drafts, and email metadata like headers—no write operations.
        'email.send',	            // Send messages only. No read or modify privileges on users' emails. Using email.send as the only scope with Gmail accounts may lead to unexpected threading behavior.
        'email.folders_and_labels',	// Read and modify folders or labels, depending on the account type.
        'email.metadata',	        // Read email metadata including headers and labels/folders, but not the message body or file attachments.
        'email.drafts',	            // Read and modify drafts. Does not include send.
        'calendar',	                // Read and modify calendars and events.
        'calendar.free_busy',       // EWS accounts should add this scope to access the free/busy endpoint.
        'calendar.read_only',	    // Read calendars and events.
        'room_resources.read_only',	// Read available room resources for an account. Room resources for Office 365 is an Admin Consent Required permission.
        'contacts',	                // Read and modify contacts.
        'contacts.read_only',	    // Read contacts.
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas webhook triggers
     */
    public const TRIGGERS = [
        'account.connected',
        'account.running',
        'account.stopped',
        'account.invalid',
        'account.sync_error',
        'message.created',
        'message.updated',
        'message.opened',
        'message.link_clicked',
        'thread.replied',
        'contact.created',
        'contact.updated',
        'contact.deleted',
        'calendar.created',
        'calendar.updated',
        'calendar.deleted',
        'event.created',
        'event.updated',
        'event.deleted',
        'job.succesful',
        'job.failed',
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
        'connectProvider'  => '/connect/detect-provider',
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

        // Outbox
        'outbox'    => '/outbox',
        'oneOutbox' => '/outbox/%s',

        // Sending
        'sending' => '/send',

        // Files
        'files'        => '/files',
        'oneFile'      => '/files/%s',
        'downloadFile' => '/files/%s/download',

        // Calendars
        'calendars'           => '/calendars',
        'oneCalendar'         => '/calendars/%s',
        'calendarFreeBusy'    => '/calendars/free-busy',
        'calendarAbility'     => '/calendars/availability',
        'calendarConsecutive' => '/calendars/availability/consecutive',

        // Events
        'events'    => '/events',
        'oneEvent'  => '/events/%s',
        'icsEvent'  => '/events/to-ics',
        'rsvpEvent' => '/send-rsvp',

        // Rooms
        'resource' => '/resources',

        // Contacts
        'contacts'       => '/contacts',
        'oneContact'     => '/contacts/%s',
        'contactPic'     => '/contacts/%s/picture',
        'contactsGroups' => '/contacts/groups',

        // Neural
        'neuralOcr'           => '/neural/ocr',
        'neuralOcrFeedback'   => '/neural/ocr/feedback',
        'neuralCate'          => '/neural/categorize',
        'neuralCateFeedback'  => '/neural/categorize/feedback',
        'neuralConv'          => '/neural/conversation',
        'neuralConvFeedback'  => '/neural/conversation/feedback',
        'neuralSign'          => '/neural/signature',
        'neuralSignFeedback'  => '/neural/signature/feedback',
        'neuralSment'         => '/neural/sentiment',
        'neuralSmentFeedback' => '/neural/sentiment/feedback',

        // Search
        'searchThreads'  => '/threads/search',
        'searchMessages' => '/messages/search',

        // Webhooks
        'webhooks'   => '/a/%s/webhooks',
        'oneWebhook' => '/a/%s/webhooks/%s',

        // JobStatuses
        'jobStatuses'  => '/job-statuses',
        'oneJobStatus' => '/job-statuses/%s',

        // Deltas
        'delta'             => '/delta',
        'deltaLongpoll'     => '/delta/longpoll',
        'deltaStreaming'    => '/delta/streaming',
        'deltaLatestCursor' => '/delta/latest_cursor',
    ];

    // ------------------------------------------------------------------------------
}
