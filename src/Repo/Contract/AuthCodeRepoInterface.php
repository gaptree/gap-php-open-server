<?php
namespace Gap\Open\Server\Repo\Contract;

use Gap\Open\Dto\AuthCodeDto;

interface AuthCodeRepoInterface
{
    public function create(AuthCodeDto $authCode): void;
    public function destroy(AuthCodeDto $authCode): void;
    public function fetch(string $code): ?AuthCodeDto;
}
