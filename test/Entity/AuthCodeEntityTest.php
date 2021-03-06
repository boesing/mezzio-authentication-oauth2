<?php

/**
 * @see       https://github.com/mezzio/mezzio-authentication-oauth2 for the canonical source repository
 * @copyright https://github.com/mezzio/mezzio-authentication-oauth2/blob/master/COPYRIGHT.md
 * @license   https://github.com/mezzio/mezzio-authentication-oauth2/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace MezzioTest\Authentication\OAuth2\Entity;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use Mezzio\Authentication\OAuth2\Entity\AuthCodeEntity;
use PHPUnit\Framework\TestCase;

class AuthCodeEntityTest extends TestCase
{
    public function testImplementsInstanceAuthCodeEntityInterface()
    {
        $entity = new AuthCodeEntity();
        $this->assertInstanceOf(AuthCodeEntityInterface::class, $entity);
    }
}
