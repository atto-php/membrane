<?php

declare(strict_types=1);

namespace Atto\Membrane;

use Atto\Framework\Response\Errors\ErrorHandler;
use Crell\ApiProblem\ApiProblem;
use Membrane\OpenAPI\Exception\CannotProcessRequest;

final class RequestProblemHandler implements ErrorHandler
{
    public function supports(\Throwable $throwable): bool
    {
        return $throwable instanceof CannotProcessRequest;
    }

    public function handle(\Throwable $throwable): ApiProblem
    {
        switch ($throwable->getCode()) {
            case CannotProcessRequest::PATH_NOT_FOUND:
                $problem = new ApiProblem(
                    'Not found',
                    'about:blank',
                );
                $problem->setStatus(404);

                break;
            case CannotProcessRequest::METHOD_NOT_FOUND:
                $problem = new ApiProblem(
                    'Method unsupported',
                    'about:blank',
                );
                $problem->setStatus(405);

                break;
            case CannotProcessRequest::CONTENT_TYPE_NOT_SUPPORTED:
                $problem = new ApiProblem(
                    'Unacceptable content type',
                    'about:blank',
                );
                $problem->setStatus(406);

                break;

            default:
                $problem = new ApiProblem(
                    'Bad Request',
                    'about:blank',
                );
                $problem->setStatus(400);
                break;
        }

        return $problem;
    }

}