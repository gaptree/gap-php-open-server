<?php
namespace Gap\Open\Server;

use Gap\Db\Contract\CnnInterface;

use Gap\Open\Server\Repo\Contract\AccessTokenRepoInterface;
use Gap\Open\Server\Repo\Contract\AppRepoInterface;
use Gap\Open\Server\Repo\Contract\UserRepoInterface;
use Gap\Open\Server\Repo\Contract\AuthCodeRepoInterface;
use Gap\Open\Server\Repo\Contract\RefreshTokenRepoInterface;

use Gap\Open\Server\Repo\AccessTokenRepo;
use Gap\Open\Server\Repo\AppRepo;
use Gap\Open\Server\Repo\UserRepo;
use Gap\Open\Server\Repo\AuthCodeRepo;
use Gap\Open\Server\Repo\RefreshTokenRepo;

class RepoManager
{
    protected $opts;
    protected $cnn;

    public function __construct(CnnInterface $cnn, array $opts = [])
    {
        $this->cnn = $cnn;
        $this->opts = $opts;
    }

    public function getAccessTokenRepo(): AccessTokenRepoInterface
    {
        if (isset($this->opts['accessToken'])) {
            return $this->opts['accessToken'];
        }

        $this->opts['accessToken'] = new AccessTokenRepo($this->cnn);
        return $this->opts['accessToken'];
    }

    public function getAppRepo(): AppRepoInterface
    {
        if (isset($this->opts['app'])) {
            return $this->opts['app'];
        }

        $this->opts['app'] = new AppRepo($this->cnn);
        return $this->opts['app'];
    }

    public function getUserRepo(): UserRepoInterface
    {
        if (isset($this->opts['app'])) {
            return $this->opts['app'];
        }

        $this->opts['app'] = new UserRepo($this->cnn);
        return $this->opts['app'];
    }

    public function getAuthCodeRepo(): AuthCodeRepoInterface
    {
        if (isset($this->opts['authCode'])) {
            return $this->opts['authCode'];
        }

        $this->opts['authCode'] = new AuthCodeRepo($this->cnn);
        return $this->opts['authCode'];
    }

    public function getRefreshTokenRepo(): RefreshTokenRepoInterface
    {
        if (isset($this->opts['refreshToken'])) {
            return $this->opts['refreshToken'];
        }

        $this->opts['refreshToken'] = new RefreshTokenRepo($this->cnn);
        return $this->opts['refreshToken'];
    }
}
