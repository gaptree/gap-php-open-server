<?php
namespace Gap\Open\Server\Repo;

use Gap\Open\Dto\UserDto;
use Gap\Dto\DateTime;

class UserRepo extends RepoBase implements Contract\UserRepoInterface
{
    protected $table = 'open_user';

    public function create(UserDto $user): void
    {
        if (!$user->created) {
            $user->created = new DateTime();
        }
        if (!$user->changed) {
            $user->changed = new DateTime();
        }
        if (!$user->logined) {
            $user->logined = new DateTime('0000-1-1');
        }
        $this->cnn->isb()
            ->insert($this->table)
            ->field(
                'userId',
                'nick',
                'zcode',
                'avt',
                'logined',
                'created',
                'changed'
            )->value()
                ->addStr($user->userId)
                ->addStr($user->nick)
                ->addStr($user->zcode)
                ->addStr($user->avt)
                ->addDateTime($user->logined)
                ->addDateTime($user->created)
                ->addDateTime($user->changed)
            ->end()
            ->execute();
    }

    public function fetch(string $userId): ?UserDto
    {
        return $this->cnn->ssb()
            ->select(
                'userId',
                'nick',
                'zcode',
                'avt',
                'logined',
                'created',
                'changed'
            )
            ->from($this->table)->end()
            ->where()
                ->expect('userId')->equal()->str($userId)
            ->end()
            ->fetch(UserDto::class);
    }
}
