<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use App\Domain\User\UserRepository;
use Exception;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /**
     * @var array{
     *   iss: string,
     * }
     */
    private $options = [
        'iss' => '',
        'aud' => '',
        'auth_time' => '',
        'user_id' => '',
        'sub' => '',
        'iat' => '',
        'exp' => '',
    ];

    /**
     * @var UserRepository $userRepository;
     */
    private $userRepository;

    public function __construct(UserRepository $repository, array $options = [])
    {
        $this->userRepository = $repository;
        $this->hydrate($options);
    }

    /**
     * Process a request in PSR-15 style and return a response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        
        $payload = $request->getAttribute('payload');

        if ($payload["iss"] !== $this->options['iss']) {
            throw new HttpUnauthorizedException($request);
        }

        try {
            $user = $this->userRepository->findUserOfUid($payload['user_id']);
        } catch (Exception $e) {
            throw new HttpUnauthorizedException($request);
        }

        switch ($user['role']) {
            case 1:
                $request = $request->withAttribute('user_auth', ['write', 'read']);
                break;
            case 2:
                $request = $request->withAttribute('user_auth', ['read']);
                break;
            default:
                $request = $request->withAttribute('user_auth', []);
                break;
        };


        $response = $handler->handle($request);

        return $response;
    }

    /**
     * Hydrate options from given array.
     *
     * @param mixed[] $data
     */
    private function hydrate(array $data = []): void
    {
        foreach ($data as $key => $value) {
            /* https://github.com/facebook/hhvm/issues/6368 */
            $key = str_replace(".", " ", $key);
            /* Or fallback to setting option directly */
            $this->options[$key] = $value;
        }
    }
}
