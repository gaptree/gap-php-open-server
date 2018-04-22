<?php
namespace Gap\Open\Server\Repo\Contract;

use Gap\Open\Dto\RefreshTokenDto;

interface RefreshTokenRepoInterface
{
    public function create(RefreshTokenDto $refreshToken): void;
    public function fetch(string $refresh): RefreshTokenDto;
}
