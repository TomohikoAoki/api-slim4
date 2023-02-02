<?php

declare(strict_types=1);

namespace App\Application\Middleware\Exception;

use Slim\Exception\HttpSpecializedException;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;


class FormValidationException extends HttpSpecializedException
{
    protected $code = 422;

    protected $message;

    public function __construct(ServerRequestInterface $request, ?string $message = null, ?Throwable $previous = null)
    {
        $this->message = $message;
        parent::__construct($request, $this->message, $previous);
    }
}
