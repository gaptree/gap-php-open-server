<?php
namespace Gap\Open\Server\Grant;

use Gap\Dto\DateTime;
use Gap\Open\Dto\AccessTokenDto;
use Gap\Open\Server\Service\UserService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Parser;

class OpenIdGrant extends GrantBase
{
    protected $userService;
    protected $issuer = 'http://www.gaptree.com';
    protected $idTokenTtl;

    private $publicKey = '';
    private $privateKey = '';

    public function setIssuer(string $issuer): self
    {
        $this->issuer = $issuer;
        return $this;
    }

    public function setPublicKey(string $publicKey): self
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    public function setPrivateKey(string $privateKey): self
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    public function idToken(string $userId, array $info = [])
    {
        $user = $this->getUserService()->fetch($userId);
        if (is_null($user)) {
            return null;
        }

        $signer = new Sha256();
        $keychain = new Keychain();

        $token = (new Builder())->setIssuer($this->issuer) // Configures the issuer (iss claim)
            //->setAudience('http://example.org') // Configures the audience (aud claim)
            //->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            //->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
            ->setExpiration($this->getIdTokenExpired()) // Configures the expiration time of the token (exp claim)
            ->set('userId', $user->userId) // Configures a new claim, called "uid"
            ->set('nick', $user->nick)
            ->set('info', $info)
            ->sign($signer, $keychain->getPrivateKey($this->privateKey))
            ->getToken(); // Retrieves the generated token

        return $token;
    }

    public function getUser(string $tokenStr)
    {
        $token = (new Parser())->parse($tokenStr);
        $signer = new Sha256();
        $keychain = new Keychain();

        if (!$token->verify($signer, $keychain->getPublicKey($this->publicKey))) {
            return null;
        }

        return $token->getClaim('user');
    }

    public function accessToken(string $appId, string $tokenStr): ?AccessTokenDto
    {
        $token = (new Parser())->parse($tokenStr);
        $signer = new Sha256();
        $keychain = new Keychain();

        if (!$token->verify($signer, $keychain->getPublicKey($this->publicKey))) {
            return null;
        }

        $accessTokenService = $this->getAccessTokenService();
        $accessToken = $accessTokenService->create([
            'appId' => $appId,
            'userId' => $token->getClaim('userId')
        ]);
        return $accessToken;
    }

    protected function getUserService(): UserService
    {
        if ($this->userService) {
            return $this->userService;
        }

        $this->userService = new UserService($this->repoManager, $this->cache);
        return $this->userService;
    }

    protected function getIdTokenTtl(): \DateInterval
    {
        if ($this->idTokenTtl) {
            return $this->idTokenTtl;
        }

        $this->idTokenTtl = new \DateInterval('P1M');
        return $this->idTokenTtl;
    }

    protected function getIdTokenExpired(): int
    {
        $expired = (new DateTime())->add($this->getIdTokenTtl());
        return $expired->getTimestamp();
    }
}
