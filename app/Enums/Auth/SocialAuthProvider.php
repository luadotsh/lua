<?php

declare(strict_types=1);

namespace App\Enums\Auth;

enum SocialAuthProvider: string
{
    case Google = 'google';
    case GitHub = 'github';

    public function label(): string
    {
        return match ($this) {
            self::Google => 'Google',
            self::GitHub => 'GitHub',
        };
    }

    /**
     * The column on users holding this provider's account id.
     */
    public function column(): string
    {
        return "{$this->value}_id";
    }

    /**
     * A provider without credentials configured is hidden everywhere, so a
     * self-hosted install that only sets up one of them shows only that one.
     */
    public function isEnabled(): bool
    {
        return filled(config("services.{$this->value}.client_id"))
            && filled(config("services.{$this->value}.client_secret"));
    }

    /**
     * @return array<int, self>
     */
    public static function enabled(): array
    {
        return array_values(array_filter(self::cases(), fn (self $p) => $p->isEnabled()));
    }
}
