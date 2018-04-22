<?php
namespace Gap\Open\Server\Service;

use Gap\Dto\DateTime;
use Gap\Open\Dto\UserDto;
use Gap\Open\Server\Repo\Contract\UserRepoInterface;

class UserService extends ServiceBase
{
    protected $ttl;

    public function create(UserDto $user): void
    {
        if ($this->cache) {
            $this->cache->set($user->userId, $user);
        }

        $this->getRepo()->create($user);
    }

    public function fetch(string $userId): ?UserDto
    {
        if ($this->cache) {
            if ($user = $this->cache->get($userId)) {
                return $user;
            }
        }
        return $this->getRepo()->fetch($userId);
    }

    protected function getRepo(): UserRepoInterface
    {
        return $this->repoManager->getUserRepo();
    }
}
