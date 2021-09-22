<?php

namespace Tests\Authentication;

use Tests\AbsCase;

/**
 * ----------------------------------------------------------------------------------
 * Hosted Test
 * ----------------------------------------------------------------------------------
 *
 * @link   https://developer.nylas.com/docs/api/#tag--Hosted-Authentication
 * @author lanlin
 * @change 2021/09/22
 *
 * @internal
 */
class NativeTest extends AbsCase
{
    // ------------------------------------------------------------------------------

    /**
     * @dataProvider dataParams
     *
     * @param array $para
     */
    public function testSendAuthorization(array $para): void
    {
        $this->mockResponse(['code' => $this->faker->postcode]);

        $data = $this->client->Authentication->Native->sendAuthorization($para);

        $this->assertTrue(!empty($data['code']));
    }

    // ------------------------------------------------------------------------------

    /**
     * test exchange the token
     */
    public function testExchangeTheToken(): void
    {
        $this->mockResponse([
            'access_token'      => 'token',
            'account_id'        => 'account-id',
            'billing_state'     => 'paid',
            'email_address'     => 'nyla@nylas.com',
            'id'                => 'id',
            'linked_at'         => 1563496685,
            'name'              => 'Nyla The Cheeta',
            'object'            => 'account',
            'organization_unit' => 'label',
            'provider'          => 'google',
            'sync_state'        => 'running',
        ]);

        $code = $this->faker->postcode;

        $data = $this->client->Authentication->Native->exchangeTheToken($code);

        $this->assertTrue(!empty($data['email_address']));
    }

    // ------------------------------------------------------------------------------

    public function dataParams(): array
    {
        return [
            'Authenticate Google Accounts' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@nylas.com',
                'provider'      => 'gmail',
                'settings'      => [
                    'google_client_id'     => '[google_api_client_id]',
                    'google_client_secret' => '[geoogle_api_client_secret]',
                    'google_refresh_token' => '[google_api_refresh_token]',
                ],
                'scopes' => 'email.read_only,calendar.read_only,contacts.read_only',
            ]],
            'Authenticate Google Account using a Service Account' => [[
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@gmail.com',
                'provider'      => 'gmail',
                'settings'      => [
                    'service_account_json' => [
                        'type'                        => 'service_account',
                        'project_id'                  => 'my-google-app-123456',
                        'private_key_id'              => '68915b4e55baac9191dd32e0be784687c6873b14',
                        'private_key'                 => "-----BEGIN PRIVATE KEY-----\nMIIE....fZ1F8=\n-----END PRIVATE KEY-----\n",
                        'client_email'                => 'test-service-account@my-google-app-123456.iam.gserviceaccount.com',
                        'client_id'                   => '10569134234239528168761',
                        'auth_uri'                    => 'https://accounts.google.com/o/oauth2/auth',
                        'token_uri'                   => 'https://oauth2.googleapis.com/token',
                        'auth_provider_x509_cert_url' => 'https://www.googleapis.com/oauth2/v1/certs',
                        'client_x509_cert_url'        => 'https://www.googleapis.com/robot/v1/metadata/x509/test-service-account%40my-google-app-123456.iam.gserviceaccount.com',
                    ],
                ],
                'scopes' => 'calendar',
            ]],
            'Authenticate Office365 Accounts' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@nylas.com',
                'provider'      => 'office365',
                'settings'      => [
                    'microsoft_client_id'     => '[microsoft_client_id]',
                    'microsoft_client_secret' => '[microsoft_client_secret]',
                    'microsoft_refresh_token' => '[microsoft_refresh_token]',
                    'redirect_uri'            => 'https://example.com/redirect',
                ],
                'scopes' => 'email.read_only,calendar.read_only,contacts.read_only',
            ]],
            'Authenticate Exchange Accounts' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@nylas.com',
                'provider'      => 'exchange',
                'settings'      => [
                    'username'             => 'nyla@nylas.com',
                    'password'             => 'MakeEmailSuckLess',
                    'exchange_server_host' => 'exchange.nylas.com',
                ],
                'scopes' => 'email.read_only,calendar.read_only,contacts.read_only',
            ]],
            'Authenticate Exchange Account using a Service Account via Password' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@nylas.com',
                'provider'      => 'exchange',
                'settings'      => [
                    'username'        => 'username',
                    'password'        => 'password',
                    'service_account' => true,
                ],
                'scopes' => 'calendar.read_only',
            ]],
            'Authenticate Exchange Account using a Service Account via OAuth' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@nylas.com',
                'provider'      => 'exchange',
                'settings'      => [
                    'microsoft_client_id'     => '[microsoft_client_id]',
                    'microsoft_client_secret' => '[microsoft_client_secret]',
                    'microsoft_refresh_token' => '[microsoft_refresh_token]',
                    'redirect_uri'            => 'https://example.com/redirect',
                    'service_account'         => true,
                ],
                'scopes' => 'calendar',
            ]],
            'Authenticate Outlook Accounts' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@outlook.com',
                'provider'      => 'outlook',
                'settings'      => [
                    'username'             => 'nyla@outlook.com',
                    'password'             => 'MakeEmailSuckLess',
                    'exchange_server_host' => 'eas.outlook.com',
                ],
                'scopes' => 'email.read_only,calendar.read_only,contacts.read_only',
            ]],
            'Authenticate known IMAP providers' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@aol.com',
                'provider'      => 'aol',
                'settings'      => [
                    'password' => 'MakeEmailSuckLess',
                ],
                'scopes' => 'email.read_only,calendar.read_only,contacts.read_only',
            ]],
            'Authenticate a generic IMAP provider' => [[
                'client_id'     => 'nylas_client_id',
                'name'          => 'Nyla the Cheetah',
                'email_address' => 'nyla@nylas.com',
                'provider'      => 'imap',
                'settings'      => [
                    'imap_host'     => 'imap.nylas.com',
                    'imap_port'     => 993,
                    'imap_username' => 'nyla',
                    'imap_password' => 'MakeEmailSuckLess',
                    'smtp_host'     => 'smtp.nylas.net',
                    'smtp_port'     => 587,
                    'smtp_username' => 'nyla',
                    'smtp_password' => 'MakeEmailSuckLess',
                    'ssl_required'  => true,
                ],
                'scopes' => 'email.read_only,calendar.read_only,contacts.read_only',
            ]],
            'Virtual Calendars' => [[
                'client_id'     => '9df4ff17888f4729ad4e1463d2547d',
                'provider'      => 'nylas',
                'scopes'        => 'calendar',
                'name'          => 'Virtual Calendar',
                'email_address' => 'nylas@nylas.com',
                'settings'      => [],
            ]],
        ];
    }

    // ------------------------------------------------------------------------------
}
