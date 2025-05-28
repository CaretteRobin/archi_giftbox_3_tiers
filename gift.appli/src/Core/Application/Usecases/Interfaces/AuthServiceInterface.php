<?php

namespace Gift\Appli\Core\Application\Usecases\Interfaces;

interface AuthServiceInterface {
    public function register(string $email, string $password): string;

    public function login(string $email, string $password): string;

    public function logout(): void;

    public function isEmailTaken(string $email): bool;
}
