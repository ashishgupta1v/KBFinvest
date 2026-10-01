<?php

namespace Tests\Feature;

use Tests\TestCase;

class CleanStaleCookiesTest extends TestCase
{
    public function test_it_instructs_browser_to_prune_legacy_stale_cookies(): void
    {
        // Simulate a request sending an old legacy cookie and a valid cookie
        $response = $this->withCookies([
            'laravel-session' => 'legacy-encrypted-value',
            'S11JTKkLcVDKISMdJ3kdcSDDe70gJTcEBvYNq6ps' => 'stale-random-cookie',
            'kb_finvest_session' => 'active-session-id',
        ])->get('/book');

        $response->assertStatus(200);

        // Assert that the obsolete cookies receive expiration / deletion headers
        $response->assertCookieExpired('laravel-session');
        $response->assertCookieExpired('S11JTKkLcVDKISMdJ3kdcSDDe70gJTcEBvYNq6ps');
    }
}
