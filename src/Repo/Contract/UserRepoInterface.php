<?php
namespace Gap\Open\Server\Repo\Contract;

use Gap\Open\Dto\UserDto;

interface UserRepoInterface
{
    public function create(UserDto $app): void;
    public function fetch(string $appId): ?UserDto;
}
