<?php
namespace Gap\Open\Server\Repo;

use Gap\Open\Dto\RefreshTokenDto;

class RefreshTokenRepo extends RepoBase implements Contract\RefreshTokenRepoInterface
{
    protected $table = 'open_refresh_token';

    public function create(RefreshTokenDto $refreshToken): void
    {
        $this->cnn->isb()
            ->insert($this->table)
            ->field(
                'refresh',
                'appId',
                'userId',
                'scope',
                'created',
                'expired'
            )->value()
                ->addStr($refreshToken->refresh)
                ->addStr($refreshToken->appId)
                ->addStr($refreshToken->userId)
                ->addStr($refreshToken->scope)
                ->addDateTime($refreshToken->created)
                ->addDateTime($refreshToken->expired)
            ->end()
            ->execute();
    }

    public function fetch(string $refresh): RefreshTokenDto
    {
        return $this->cnn->ssb()
            ->select(
                'refresh',
                'appId',
                'userId',
                'scope',
                'created',
                'expired'
            )
            ->from($this->table)->end()
            ->where()
                ->expect('refresh')->equal()->str($refresh)
            ->end()
            ->fetch(RefreshTokenDto::class);
    }
}
