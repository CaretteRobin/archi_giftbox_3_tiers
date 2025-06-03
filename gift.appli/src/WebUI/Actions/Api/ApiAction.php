<?php
// /gift.appli/src/WebUI/Actions/Api/ApiAction.php

namespace Gift\Appli\WebUI\Actions\Api;

use Psr\Http\Message\ResponseInterface as Response;

abstract class ApiAction
{
    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}