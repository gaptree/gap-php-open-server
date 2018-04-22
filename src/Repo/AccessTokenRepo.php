<?php
namespace Gap\Open\Server\Repo;

use Gap\Open\Dto\AccessTokenDto;

class AccessTokenRepo extends RepoBase implements Contract\AccessTokenRepoInterface
{
    protected $table = 'open_access_token';

    public function create(AccessTokenDto $accessToken): void
    {
        $this->cnn->isb()
            ->insert($this->table)
            ->field(
                'token',
                'appId',
                'userId',
                'refresh',
                'scope',
                'created',
                'expired'
            )->value()
                ->addStr($accessToken->token)
                ->addStr($accessToken->appId)
                ->addStr($accessToken->userId)
                ->addStr($accessToken->refresh)
                ->addStr($accessToken->scope)
                ->addDateTime($accessToken->created)
                ->addDateTime($accessToken->expired)
            ->end()
            ->execute();
    }

    public function fetch(string $token): AccessTokenDto
    {
        return $this->cnn->ssb()
            ->select(
                'token',
                'appId',
                'userId',
                'refresh',
                'scope',
                'created',
                'expired'
            )
            ->from($this->table)->end()
            ->where()
                ->expect('token')->equal()->str($token)
            ->end()
            ->fetch(AccessTokenDto::class);
    }
}
