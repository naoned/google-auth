<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Domain;

interface WhitelistChecker
{
    public function allow(string $mail): bool;
}
