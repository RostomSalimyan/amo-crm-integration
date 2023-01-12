<?php

namespace App\Services;

use AmoCRM\Client\AmoCRMApiClient;
use App\Models\AccessToken;
use Carbon\Carbon;

final class AmoCrmService
{
    private AmoCRMApiClient $client;

    private $code;
    private $baseDomain;
    private $apiClient;

    /**
     * @param AmoCRMApiClient $client
     */
    public function __construct(AmoCRMApiClient $client)
    {
        $this->client = $client;
        $this->registerConfigVariables();
    }

    private function createOrUpdateToken($apiClient = null) // Репозиторий
    {
        $accessTokenModel = AccessToken::query()->get()->last();

        if (is_null($accessTokenModel)) {
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($this->getCode());
            return AccessToken::query()->create([
                'access_token' => $accessToken->getToken(),
                'refresh_token' => $accessToken->getRefreshToken(),
                'expires' => $accessToken->getExpires()
            ]);
        }

        if ($this->isTokenExpired($accessTokenModel->expires)) {
            $accessTokenModel->delete();
            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($this->getCode());
            return $accessTokenModel
                ->create([
                    'access_token' => $accessToken->getToken(),
                    'refresh_token' => $accessToken->getRefreshToken(),
                    'expires' => $accessToken->getExpires()
                ]);
        }

        return $accessTokenModel;
    }

    public function connect()
    {
        $apiClient = $this->client->setAccountBaseDomain($this->getBaseDomain());
        $accessToken = $this->createOrUpdateToken($apiClient)->toArray();

        $accessToken = new \League\OAuth2\Client\Token\AccessToken($accessToken);

        $this->apiClient = $apiClient->setAccessToken($accessToken) // отдельный метод
        ->setAccountBaseDomain($this->getBaseDomain())
            ->onAccessTokenRefresh(
                function (\League\OAuth2\Client\Token\AccessTokenInterface $accessToken, string $baseDomain) {
                    saveToken([
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires()
                    ]);
                });

        return $this;
    }

    public function getLeads()
    {
        return $this->apiClient->leads()->get();
    }

    private function getCode()
    {
        return $this->code;
    }

    private function getBaseDomain()
    {
        return $this->baseDomain;
    }


    private function isTokenExpired($tokenDate): bool
    {
        $currentDate = Carbon::now('Europe/Moscow')->timestamp;

        return $currentDate > $tokenDate;
    }

    private function registerConfigVariables(): void
    {
        $this->code = config('services.code');
        $this->baseDomain = config('services.base-domain');
    }

}
