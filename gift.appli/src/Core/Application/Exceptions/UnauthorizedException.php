<?php

namespace gift\app\WebUI\Exceptions;

class UnauthorizedException extends \Exception
{
    public function __construct(string $message = "Accès non autorisé", int $code = 403)
    {
        parent::__construct($message, $code);
    }
}