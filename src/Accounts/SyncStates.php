<?php

namespace Nylas\Accounts;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Account Sync States
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class SyncStates
{
    // ------------------------------------------------------------------------------

    public const ENUMS =
    [
        'initializing',
        'downloading',
        'running',
        'invalid-credentials',
        'stopped',
        'exception',
        'sync-error',
    ];

    // ------------------------------------------------------------------------------

    public const INFOS =
    [
        'running'             => 'Sync is successfully running. No action required. It means we are up to date and listening for new changes.',
        'stopped'             => 'Sync is stopped. This can happen for a variety of reasons. If you are adding accounts beyond the limits of your free trial period, some may be stopped until you upgrade your Nylas application. It can also occur when we repeatedly encounter unexpected errors. These errors most commonly originate from incompatibilities with upstream providers or temporary outages.',
        'exception'           => 'This can occur if an upstream provider returns an error our sync engine does not yet understand. Please contact support@nylas.com for accounts in this state.',
        'sync-error'          => 'This means an unexpected error was raised while syncing an account. Please contact support@nylas.com for accounts in this state.',
        'initializing'        => 'The account is freshly connected with no known problems. This should be the very first momentary state of an account and is part of normal operation.',
        'downloading'         => 'The account is in normal operation but has not yet finished its initial sync.',
        'invalid-credentials' => 'Authenticating failure with mail server. You should prompt the user to re-authorize.',
    ];

    // ------------------------------------------------------------------------------
}
