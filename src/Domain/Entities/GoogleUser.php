<?php

declare(strict_types = 1);

namespace Naoned\GoogleAuth\Domain\Entities;

class GoogleUser
{
    private
        $mail,
        $name,
        $avatar;

    public function __construct(string $mail, ?string $name, string $avatar)
    {
        $this->mail = $mail;
        $this->name = empty($name) ? null : $name;
        $this->avatar = $avatar;
    }

    public function mail(): string
    {
        return $this->mail;
    }

    public function name(): string
    {
        return $this->name ?? $this->mail;
    }

    /**
     * @return string url
     */
    public function avatar(): string
    {
        return $this->avatar;
    }
}
