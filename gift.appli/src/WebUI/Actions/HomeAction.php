<?php

namespace Gift\Appli\WebUI\Actions;

use Gift\Appli\Core\Application\Usecases\Interfaces\CatalogueServiceInterface;
use Gift\Appli\WebUI\Providers\Interfaces\AuthProviderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class HomeAction
{
    private CatalogueServiceInterface $catalogueService;
    private Twig $twig;
    private AuthProviderInterface $authProvider;

    public function __construct(CatalogueServiceInterface $catalogueService, Twig $twig, AuthProviderInterface $authProvider)
    {
        $this->catalogueService = $catalogueService;
        $this->twig = $twig;
        $this->authProvider = $authProvider;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $user = $this->authProvider->getLoggedUser();

        $categories = array_slice($this->catalogueService->getCategories(), 0, 3);

        $params = ['categories' => $categories];
        if ($user) {
            $params['user'] = $user;
        }
        return $this->twig->render($response, 'pages/home.twig', $params);
    }
}
