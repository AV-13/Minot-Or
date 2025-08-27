<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Enum\UserRole;
use App\Service\SecurityHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SecurityHelperTest extends TestCase
{
    public function testGetUserReturnsUser()
    {
        $user = new User();
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $helper = new SecurityHelper($security);
        $this->assertSame($user, $helper->getUser());
    }

    public function testGetUserThrowsExceptionIfNotAuthenticated()
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $helper = new SecurityHelper($security);

        $this->expectException(AccessDeniedHttpException::class);
        $helper->getUser();
    }

    public function testHasRoleReturnsTrueIfRoleMatches()
    {
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn(UserRole::Baker);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $helper = new SecurityHelper($security);
        $this->assertTrue($helper->hasRole(UserRole::Baker->value));
    }

    public function testHasRoleReturnsFalseIfRoleDoesNotMatch()
    {
        $user = $this->createMock(User::class);
        $user->method('getRole')->willReturn(UserRole::Maintenance);

        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $helper = new SecurityHelper($security);
        $this->assertFalse($helper->hasRole(UserRole::OrderPreparer->value));
    }
}