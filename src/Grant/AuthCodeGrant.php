<?php
namespace Gap\Open\Server\Grant;

use Gap\Open\Dto\AuthCodeDto;
use Gap\Open\Dto\AccessTokenDto;
use Gap\Open\Server\Service\AuthCodeService;

class AuthCodeGrant extends GrantBase
{
    protected $authCodeService;

    public function authCode(
        string $appId,
        string $userId,
        string $redirectUrl,
        string $scope = ''
    ): ?AuthCodeDto {
        $authCodeService = $this->getAuthCodeService();
        $authCode = $authCodeService->generate(
            $appId,
            $userId,
            $redirectUrl,
            $scope
        );
        $authCodeService->create($authCode);
        return $authCode;
    }

    public function accessToken(string $appId, string $code): ?AccessTokenDto
    {
        $authCodeService = $this->getAuthCodeService();
        $authCode = $authCodeService->fetch($code);
        if (is_null($authCode)) {
            return null;
        }

        if ($authCode->appId !== $appId) {
            throw new \Exception('appId not match');
        }
        if (empty($authCode->userId)) {
            throw new \Exception('userId cannot be empty');
        }

        $accessTokenService = $this->getAccessTokenService();
        $refreshTokenService = $this->getRefreshTokenService();

        $this->cnn->trans()->begin();
        $userId = $authCode->userId;
        $scope = $authCode->scope;

        try {
            $refreshToken = $refreshTokenService->create([
                'appId' => $appId,
                'userId' => $userId,
                'scope' => $scope
            ]);

            $accessToken = $accessTokenService->create([
                'appId' => $appId,
                'userId' => $userId,
                'scope' => $scope,
                'refresh' => $refreshToken->refresh
            ]);

            $authCodeService->destroy($authCode);
        } catch (\Exception $exp) {
            $this->cnn->trans()->rollback();
            throw $exp;
        }

        $this->cnn->trans()->commit();
        return $accessToken;
    }

    protected function getAuthCodeService(): AuthCodeService
    {
        if ($this->authCodeService) {
            return $this->authCodeService;
        }

        $this->authCodeService = new AuthCodeService(
            $this->repoManager,
            $this->cache
        );
        return $this->authCodeService;
    }
}
