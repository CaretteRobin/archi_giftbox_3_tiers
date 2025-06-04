<?php
declare(strict_types=1);

namespace Gift\Appli\WebUI\Providers\Interfaces;

use Gift\Appli\Core\Domain\Entities\User;

interface UserProviderInterface
{
    public function store(User $user): void;
    public function current(): ?User;
    public function clear(): void;
    public function isLoggedIn(): bool;
}