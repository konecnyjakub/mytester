<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteSecurity;

use MyTester\Attributes\Group;
use MyTester\Attributes\RequiresEnvVariable;
use MyTester\Attributes\TestSuite;
use MyTester\Bridges\NetteDI\ContainerFactory;
use MyTester\TestCase;
use Nette\Security\IIdentity;
use Nette\Security\User;

/**
 * Test suite for trait TUser
 *
 * @author Jakub Konečný
 */
#[TestSuite("TPresenter")]
#[Group("nette")]
#[RequiresEnvVariable("MYTESTER_NETTE_DI")]
final class TUserTest extends TestCase
{
    use TUser;

    public function testLoginStatus(): void
    {
        $this->assertFalse($this->isUserLoggedIn());

        /** @var User $user */
        $user = ContainerFactory::create(false)->getByType(User::class);
        $this->login(1, ["tester",], ["one" => "abc", "two" => "def",]);
        $this->assertTrue($this->isUserLoggedIn());
        /** @var IIdentity $identity */
        $identity = $user->identity;
        $this->assertSame(1, $user->id);
        $this->assertSame(1, $identity->getId());
        $this->assertSame(["tester",], $user->roles);
        $this->assertSame(["tester",], $identity->getRoles());
        $this->assertSame(["one" => "abc", "two" => "def",], $identity->getData());

        $this->logout();
        $this->assertFalse($this->isUserLoggedIn());
        $this->assertNull($user->identity);
    }
}
