<?php
namespace Gap\Open\Server\Grant;

use Gap\Db\Contract\CnnInterface;
use Psr\SimpleCache\CacheInterface;
use Gap\Open\Server\RepoManager;
use Gap\Open\Server\Service\AccessTokenService;
use Gap\Open\Server\Service\RefreshTokenService;

abstract class GrantBase
{
    protected $cnn;
    protected $repoManager;
    protected $cache;

    protected $accessTokenService;
    protected $refreshTokenService;

    public function __construct(
        CnnInterface $cnn,
        RepoManager $repoManager,
        ?CacheInterface $cache = null
    ) {
        $this->cnn = $cnn;
        $this->repoManager = $repoManager;
        $this->cache = $cache;
    }

    protected function getAccessTokenService(): AccessTokenService
    {
        if ($this->accessTokenService) {
            return $this->accessTokenService;
        }

        $this->accessTokenService = new AccessTokenService(
            $this->repoManager,
            $this->cache
        );
        return $this->accessTokenService;
    }

    protected function getRefreshTokenService(): RefreshTokenService
    {
        if ($this->refreshTokenService) {
            return $this->refreshTokenService;
        }

        $this->refreshTokenService = new RefreshTokenService(
            $this->repoManager,
            $this->cache
        );
        return $this->refreshTokenService;
    }
}
