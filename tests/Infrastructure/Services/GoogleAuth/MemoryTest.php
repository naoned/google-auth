<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Infrastructure\Services\GoogleAuth;

use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    public function testMemory(): void
    {
        $login = 'myLoginUrl';
        $logout = 'myLogoutUrl';
        $myMail = 'myMail';

        $auth = new Memory($login, $logout, function() use ($myMail) {
            return $myMail;
        });

        $this->assertSame($login, $auth->loginUrl());
        $this->assertSame($logout, $auth->logoutUrl('myRoute'));
        $this->assertSame($myMail, $auth->loginProcess());
    }
}
