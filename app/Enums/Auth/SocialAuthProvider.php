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
     * A provider needs both to appear: the switch in config/lua.php, and
     * credentials in config/services.php.
     *
     * The credential check is what keeps a self-hosted install from rendering
     * a button that leads straight to an OAuth error, and the switch is what
     * lets you turn a provider off without deleting the credentials to do it.
     */
    public function isEnabled(): bool
    {
        return (bool) config("lua.auth.{$this->value}")
            && filled(config("services.{$this->value}.client_id"))
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
