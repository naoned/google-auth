<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Domain\Services\WhitelistCheckers;

use Naoned\GoogleAuth\Domain\WhitelistChecker;

class ArrayBased implements WhitelistChecker
{
    private
        $domains,
        $mails;

    public function __construct(array $allowedDomains, array $allowedMails)
    {
        $this->domains = $allowedDomains;
        $this->mails = $allowedMails;
    }

    public function allow(string $mail): bool
    {
        if(empty($this->domains) && empty($this->mails))
        {
            return true;
        }

        foreach($this->mails as $restrictiveMail)
        {
            if($mail === $restrictiveMail)
            {
                return true;
            }
        }

        foreach($this->domains as $domain)
        {
            if(preg_match('/@' . $domain . '$/', $mail))
            {
                return true;
            }
        }

        return false;
    }
}
