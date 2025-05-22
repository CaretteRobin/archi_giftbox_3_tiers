<?php

namespace Gift\Appli\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Exception\HttpException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Views\Twig;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    private Twig $twig;
    private bool $displayErrorDetails;

    /**
     * @param Twig  $twig                Instance de Twig pour le rendu.
     * @param bool  $displayErrorDetails Affiche ou non le détail des erreurs PHP.
     */
    public function __construct(Twig $twig, bool $displayErrorDetails = true)
    {
        $this->twig               = $twig;
        $this->displayErrorDetails = $displayErrorDetails;

        // Facultatif : forcer l’affichage des erreurs PHP si souhaité
        if ($this->displayErrorDetails) {
            ini_set('display_errors', '1');
            error_reporting(E_ALL);
        }
    }

    public function process(Request $request, Handler $handler): Response
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $exception) {
            /* --------------------------------------------------------
             * Construction d’une réponse d’erreur personnalisée
             * --------------------------------------------------------*/
            $statusCode   = 500;
            $publicMsg    = "Une erreur s'est produite";

            if ($exception instanceof HttpNotFoundException) {
                $statusCode = 404;
                $publicMsg  = "La page demandée n'existe pas";
            } elseif ($exception instanceof HttpException) {
                $statusCode = $exception->getCode() ?: 500;
                $publicMsg  = $exception->getMessage();
            }

            $details = $this->displayErrorDetails
                ? $exception->getMessage() . "\n\n" . $exception->getTraceAsString()
                : '';

            $response = (new ResponseFactory())->createResponse($statusCode);

            return $this->twig->render($response, 'error.twig', [
                'code'         => $statusCode,
                'message'      => $publicMsg,
                'errorDetails' => $details,
            ]);
        }
    }
}
