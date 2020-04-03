<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;

class BaseController
{
    /**
     * @param Response $response
     * @param string $message
     *
     * @return Response
     */
    protected function responseServerError(Response $response, string $message = '') : Response
    {
        return $this->response($response, [
            'reason' => $message ?: 'Unexpected error.'
        ], 500);
    }

    /**
     * @param Response $response
     *
     * @return Response
     */
    protected function responseNotFound(Response $response) : Response
    {
        return $this->response($response, [], 404);
    }

    /**
     * @param Response $response
     * @param string $message
     *
     * @return Response
     */
    protected function responseBadRequest(Response $response, string $message = '') : Response
    {
        return $this->response($response, [
            'reason' => $message ?: 'Bad request.'
        ], 400);
    }

    /**
     * @param Response $response
     * @param array $data
     * @param int $statusCode
     *
     * @return Response
     */
    protected function response(Response $response, $data = [], $statusCode = 200) : Response
    {
        $payload = json_encode($data);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}