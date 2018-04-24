<?php
namespace Gap\Open\Server\Repo\Contract;

use Gap\Open\Dto\AppDto;

interface AppRepoInterface
{
    public function fetch(string $appId): ?AppDto;
    public function create(AppDto $app): void;
    public function disable(AppDto $app): void;
}
