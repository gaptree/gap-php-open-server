<?php
namespace Gap\Open\Server;

//use Gap\Db\Contract\CnnInterface;
//use Psr\SimpleCache\CacheInterface;

// https://www.php-fig.org/psr/psr-16/

class OpenServer
{
    private $cnn;
    private $cache;
    private $repoManager;

    private $authCodeGrant;
    private $openIdGrant;
    private $clientCdGrant;

    private $appService;
    private $accessTokenService;

    private $publicKey;
    private $privateKey;
    private $issuer;

    //public function __construct(?CnnInterface $cnn = null, ?CacheInterface $cache = null, array $opts = [])
    public function __construct(array $opts = [])
    {
        $this->cnn = $opts['cnn'] ?? null;
        $this->cache = $opts['cache'] ?? null;
        $this->repoManager = new RepoManager($this->cnn, ($opts['repo'] ?? []));

        $this->publicKey = $opts['publicKey'] ?? '';
        $this->privateKey = $opts['privateKey'] ?? '';
        $this->issuer = $opts['issuer'] ?? 'http://www.gaptree.com';
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
        $this->openIdGrant->setPublicKey($this->publicKey);
        $this->openIdGrant->setPrivateKey($this->privateKey);
        $this->openIdGrant->setIssuer($this->issuer);
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

    public function accessTokenService(): Service\AccessTokenService
    {
        if ($this->accessTokenService) {
            return $this->accessTokenService;
        }

        $this->accessTokenService = new Service\AccessTokenService($this->repoManager, $this->cache);
        return $this->accessTokenService;
    }
}
