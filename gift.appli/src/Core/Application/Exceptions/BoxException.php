<?php

namespace Gift\Appli\Core\Application\Exceptions;

use Exception;

class BoxException extends Exception
{
    // Constantes pour les codes d'erreur
    const int INVALID_BOX = 10;
    const int BOX_NOT_MODIFIABLE = 11;
    const int INVALID_PRESTATION = 20;
    const int INVALID_QUANTITY = 21;
    const int INSUFFICIENT_PRESTATIONS = 30;
    const int UNAUTHORIZED_USER = 40;
    const int INVALID_DATA = 50;
}