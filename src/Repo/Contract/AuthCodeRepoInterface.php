<?php
namespace Gap\Open\Server\Repo\Contract;

use Gap\Open\Dto\AuthCodeDto;

interface AuthCodeRepoInterface
{
    public function create(AuthCodeDto $authCode): void;
}
