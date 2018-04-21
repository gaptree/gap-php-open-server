<?php
namespace Gap\Open\Server\Repo\Contract;

use Gap\Open\Dto\AccessTokenDto;

interface AccessTokenRepoInterface
{
    public function create(AccessTokenDto $accessToken): void;
}
