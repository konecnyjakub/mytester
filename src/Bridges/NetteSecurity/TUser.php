<?php
declare(strict_types=1);

namespace MyTester\Bridges\NetteSecurity;

use MyTester\Bridges\NetteDI\ContainerFactory;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\SimpleIdentity;
use Nette\Security\User;

trait TUser
{
    /**
     * @param list<string> $roles
     * @param mixed[] $data
     * @throws AuthenticationException
     */
    protected function login(IIdentity|int $id, array $roles = [], array $data = []): void
    {
        $identity = $id instanceof IIdentity ? $id : new SimpleIdentity($id, $roles, $data);
        /** @var User $user */
        $user = ContainerFactory::create(false)->getByType(User::class);
        $user->login($identity);
    }

    protected function logout(): void
    {
        /** @var User $user */
        $user = ContainerFactory::create(false)->getByType(User::class);
        $user->logout(true);
    }

    protected function isUserLoggedIn(): bool
    {
        /** @var User $user */
        $user = ContainerFactory::create(false)->getByType(User::class);
        return $user->isLoggedIn();
    }
}
