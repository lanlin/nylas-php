<?php

declare(strict_types = 1);

namespace Nylas\Utilities;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul API List
 * ----------------------------------------------------------------------------------
 *
 * @see https://developer.nylas.com/docs/new/#product-releases
 * @see https://developer.nylas.com/docs/new/release-notes/all/
 *
 * @version 2.7 (2023/07/21)
 *
 * @author lanlin
 * @change 2023/07/24
 */
class API
{
    // ------------------------------------------------------------------------------

    /**
     * nylas common server list array
     *
     * @see https://developer.nylas.com/docs/the-basics/platform/data-residency/
     */
    public const SERVER = [
        'oregon'  => 'https://api.nylas.com',
        'ireland' => 'https://ireland.api.nylas.com',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas scheduler server list array
     *
     * @see https://developer.nylas.com/docs/the-basics/platform/data-residency/
     */
    public const SERVER_SCHEDULER = [
        'oregon'  => 'https://api.schedule.nylas.com',
        'ireland' => 'https://ireland.api.schedule.nylas.com',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas providers (for Native Authentication only)
     *
     * @see https://developer.nylas.com/docs/api/supported-providers/
     * @see https://developer.nylas.com/docs/api/v2/#post-/connect/authorize
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
        'graph',
        'office365',
        'nylas',
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas account status
     *
     * @see https://developer.nylas.com/docs/developer-guide/manage-accounts/account-sync-status/#account-management
     */
    public const STATUS = [
        'valid',               // All emails for folders, contacts, and calendars are syncing reliably.
        'invalid',             // The account has an authorization issue and needs to be re-authenticated. Learn more about Account re-authentication.
        'stopped',             // An account stops syncing if it repeatedly encounters the same error or is unable to access the email server. In cases where an account has stopped, you can try to restart it using the downgrade and upgrade endpoints. Learn more about Account re-authentication. If the account continues to fall into a stopped sync state, please contact us.
        'running',             // All emails for folders, contacts, and calendars are syncing reliably.
        'partial',             // See Partial https://developer.nylas.com/docs/developer-guide/manage-accounts/account-sync-status/#partial.
        'exception',           // This can occur if an upstream provider returns an error that Nylas's sync engine doesn't yet understand. Please contact us for accounts in this state.
        'sync-error',          // An unexpected error was raised while syncing an account. Please contact us for accounts in this state.
        'downloading',         // All folders are connected and the account is in the process of syncing all historical messages on the account. Depending on the size of the account and the speed of the connection between Nylas and the email server, this can take up to 24 hours or more to complete. During this time, the account is usable for sending messages and receiving new email messages.
        'initializing',        // The account has been authenticated on the Nylas platform and is in the process of connecting to all the account's folders. Accounts that use email.send as the only scope will always be in an initializing state. Nylas uses folders to determine sync status. email.send doesn't fetch folders.
        'invalid-credentials', // You can only continue to use an account with our API as long as the <ACCESS_TOKEN> is valid. Sometimes, this token is invalidated by the provider when connection settings are changed or by the end-user when their password is changed. When this happens, reauthenticate the account and generate a new <ACCESS_TOKEN> for the account. Learn more about Account re-authentication.
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas scopes
     *
     * @see https://developer.nylas.com/docs/the-basics/authentication/authentication-scopes/#nylas-scopes
     */
    public const SCOPES = [
        'email',                    // Send and modify all messages, threads, file attachments, and read email metadata like headers
        'email.send',               // Send messages only. No read or modify privileges on users' emails. Using email.send as the only scope with Gmail accounts may lead to unexpected threading behavior. Accounts using this as the only scope will also always be in an initializing state.
        'email.modify',             // Read and modify all messages, threads, file attachments, and read email metadata like headers. Does not include send.
        'email.drafts',             // Read and modify drafts. Does not include send.
        'email.metadata',           // Read email metadata including headers and labels/folders, but not the message body or file attachments.
        'email.read_only',          // Read all messages, threads, file attachments, drafts, and email metadata like headers. No write operations.
        'email.folders_and_labels', // Read and modify folders or labels, depending on the account type.
        'contacts',                 // Read and modify contacts.
        'contacts.read_only',       // Read contacts.
        'calendar',                 // Read and modify calendars and events.
        'calendar.free_busy',       // Exchange WebSync (EWS) accounts should add this scope to access the /free-busy endpoint.
        'calendar.read_only',       // Read calendars and events.
        'room_resources.read_only', // Read available room resources for an account. Room resources for Office 365 is an admin consent required permission.
    ];

    // ------------------------------------------------------------------------------

    /**
     * nylas webhook triggers
     *
     * @see https://developer.nylas.com/docs/developer-guide/webhooks/set-up-webhooks/#notification-triggers
     */
    public const TRIGGERS = [
        'account.connected',    // An account has been connected to your app.
        'account.invalid',      // An account has invalid credentials and must re-authenticate.
        'account.running',      // An account is syncing and running properly even if the account is in a partial state.
        'account.stopped',      // An account was stopped or cancelled.
        'account.sync_error',   // An account has a sync error and is no longer syncing.
        'message.created',      // A new message was sent or received.
        'message.link_clicked', // A link in a tracked message has been clicked by a message participant. Enable using Message Tracking.
        'message.opened',       // A tracked message has been opened by a message participant. Enable using Message Tracking.
        'message.updated',      // An update to a message occurred.
        'thread.replied',       // A participant replied to a tracked thread.
        'contact.created',      // A contact has been added to an account.
        'contact.updated',      // A contact has been updated on an account.
        'contact.deleted',      // A contact has been deleted from an account.
        'calendar.created',     // A calendar has been added to an account.
        'calendar.updated',     // A calendar has been updated on an account.
        'calendar.deleted',     // A calendar has been deleted from an account.
        'event.created',        // An event has been added to an account.
        'event.updated',        // An event has been updated on an account. This can include event changes and event deletions.
        'event.deleted',        // An event has been deleted from an account.
        'job.successful',       // Job was successfully synced back to the provider for a given job_status_id.
        'job.failed',           // The job has permanently failed after retrying 20 times. The changes have not synced with the provider.
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

        // Schedulers
        // https://developer.nylas.com/docs/api/v2/scheduler/#overview
        'scheduler'          => '/manage/pages',
        'oneScheduler'       => '/manage/pages/%s',
        'schedulerCalendars' => '/manage/pages/%s/calendars',
        'schedulerUploadImg' => '/manage/pages/%s/upload-image',
    ];

    // ------------------------------------------------------------------------------
}
