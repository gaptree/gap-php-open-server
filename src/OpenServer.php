<?php
namespace Gap\Open\Server;

use Gap\Db\Contract\CnnInterface;
use Psr\SimpleCache\CacheInterface;

// https://www.php-fig.org/psr/psr-16/

class OpenServer
{
    protected $cnn;
    protected $cache;
    protected $repoManager;

    protected $authCodeGrant;
    protected $openIdGrant;
    protected $clientCdGrant;

    protected $appService;

    public function __construct(?CnnInterface $cnn, ?CacheInterface $cache = null, array $repoOpts = []) //)
    {
        $this->cnn = $cnn;
        $this->cache = $cache;
        $this->repoManager = new RepoManager($this->cnn, $repoOpts);
    }

    public function authCodeGrant(): Grant\AuthCodeGrant
    {
        if ($this->authCodeGrant) {
            return $this->authCodeGrant;
        }

        $this->authCodeGrant = new Grant\AuthCodeGrant($this->cnn, $this->repoManager, $this->cache);
        return $this->authCodeGrant;
    }

    public function openIdGrant(): Grant\OpenIdGrant
    {
        if ($this->openIdGrant) {
            return $this->openIdGrant;
        }

        $this->openIdGrant = new Grant\OpenIdGrant($this->cnn, $this->repoManager, $this->cache);
        return $this->openIdGrant;
    }

    public function clientCdGrant(): Grant\ClientCdGrant
    {
        if ($this->clientCdGrant) {
            return $this->clientCdGrant;
        }

        $this->clientCdGrant = new Grant\ClientCdGrant($this->cnn, $this->repoManager, $this->cache);
        return $this->clientCdGrant;
    }

    public function appService(): Service\AppService
    {
        if ($this->appService) {
            return $this->appService;
        }

        $this->appService = new Service\AppService($this->repoManager, $this->cache);
        return $this->appService;
    }
}
