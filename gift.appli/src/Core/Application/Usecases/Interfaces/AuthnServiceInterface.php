<?php

namespace Gift\Appli\Core\Application\Usecases\Interfaces;

use Gift\Appli\Core\Domain\Entities\User;

interface AuthnServiceInterface {
    public function register(string $email, string $password): User;

    public function login(string $email, string $password): User;

}
