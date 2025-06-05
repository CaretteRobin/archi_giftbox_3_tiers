<?php

namespace Gift\Appli\Core\Application\Exceptions;

use Exception;

class BoxException extends Exception
{
    // Constantes pour les codes d'erreur
    const INVALID_BOX = 10;
    const BOX_NOT_MODIFIABLE = 11;
    const INVALID_PRESTATION = 20;
    const INVALID_QUANTITY = 21;
    const INSUFFICIENT_PRESTATIONS = 30;
    const UNAUTHORIZED_USER = 40;
    const INVALID_DATA = 50;
    const DELETE_ERROR = 60;
}