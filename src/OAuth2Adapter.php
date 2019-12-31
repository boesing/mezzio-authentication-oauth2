<?php

/**
 * @see       https://github.com/mezzio/mezzio-authentication-oauth2 for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authentication-oauth2/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authentication-oauth2/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Mezzio\Authentication\OAuth2;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Mezzio\Authentication\AuthenticationInterface;
use Mezzio\Authentication\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class OAuth2Adapter implements AuthenticationInterface
{
    /**
     * @var ResourceServer
     */
    protected $resourceServer;

    /**
     * @var callable
     */
    protected $responseFactory;

    /**
     * @var callable
     */
    protected $userFactory;

    public function __construct(
        ResourceServer $resourceServer,
        callable $responseFactory,
        callable $userFactory
    ) {
        $this->resourceServer = $resourceServer;
        $this->responseFactory = function () use ($responseFactory) : ResponseInterface {
            return $responseFactory();
        };
        $this->userFactory = function (
            string $identity,
            array $roles = [],
            array $details = []
        ) use ($userFactory) : UserInterface {
            return $userFactory($identity, $roles, $details);
        };
    }

    /**
     * {@inheritDoc}
     */
    public function authenticate(ServerRequestInterface $request) : ?UserInterface
    {
        try {
            $result = $this->resourceServer->validateAuthenticatedRequest($request);
            $userId = $result->getAttribute('oauth_user_id', null);
            $clientId = $result->getAttribute('oauth_client_id', null);
            if (isset($userId)) {
                return ($this->userFactory)(
                    $userId,
                    [],
                    [
                        'oauth_user_id' => $userId,
                        'oauth_client_id' => $clientId,
                        'oauth_access_token_id' => $result->getAttribute('oauth_access_token_id', null),
                        'oauth_scopes' => $result->getAttribute('oauth_scopes', null)
                    ]
                );
            }
        } catch (OAuthServerException $exception) {
            return null;
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function unauthorizedResponse(ServerRequestInterface $request) : ResponseInterface
    {
        return ($this->responseFactory)()
            ->withHeader(
                'WWW-Authenticate',
                'Bearer realm="OAuth2 token"'
            )
            ->withStatus(401);
    }
}
