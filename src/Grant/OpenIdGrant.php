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

    public function idToken(string $userId, string $privateKey)
    {
        $user = $this->getUserService()->fetch($userId);
        if (is_null($user)) {
            return null;
        }

        $signer = new Sha256();
        $keychain = new Keychain();

        $token = (new Builder())->setIssuer($this->getIssuer()) // Configures the issuer (iss claim)
            //->setAudience('http://example.org') // Configures the audience (aud claim)
            //->setId('4f1g23a12aa', true) // Configures the id (jti claim), replicating as a header item
            ->setIssuedAt(time()) // Configures the time that the token was issue (iat claim)
            //->setNotBefore(time() + 60) // Configures the time that the token can be used (nbf claim)
            ->setExpiration($this->getIdTokenExpired()) // Configures the expiration time of the token (exp claim)
            ->set('userId', $user->userId) // Configures a new claim, called "uid"
            ->set('nick', $user->nick)
            ->sign($signer, $keychain->getPrivateKey($privateKey))
            ->getToken(); // Retrieves the generated token

        return $token;
    }

    public function accessToken(string $appId, string $tokenStr, string $publicKey): ?AccessTokenDto
    {
        $token = (new Parser())->parse($tokenStr);
        $signer = new Sha256();
        $keychain = new Keychain();

        if (!$token->verify($signer, $keychain->getPublicKey($publicKey))) {
            return null;
        }

        $accessTokenService = $this->getAccessTokenService();
        $accessToken = $accessTokenService->generate(
            $appId,
            $token->getClaim('userId')
        );
        $accessTokenService->create($accessToken);
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

    protected function getIssuer(): string
    {
        return $this->issuer;
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
