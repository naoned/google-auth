<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Domain\Services\WhitelistCheckers;

use Naoned\GoogleAuth\Domain\WhitelistChecker;
use Puzzle\Configuration;

class PuzzleConfiguration implements WhitelistChecker
{
    private
        $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function allow(string $mail): bool
    {
        $domains = $this->configuration->read('restrictions/domains', []);
        $mails = $this->configuration->read('restrictions/mails', []);

        if(empty($domains) && empty($mails))
        {
            return true;
        }

        foreach($mails as $restrictiveMail)
        {
            if($mail === $restrictiveMail)
            {
                return true;
            }
        }

        foreach($domains as $domain)
        {
            if(preg_match('/@' . $domain . '$/', $mail))
            {
                return true;
            }
        }

        return false;
    }
}
