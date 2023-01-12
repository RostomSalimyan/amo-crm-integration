<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\ResourceOwnerAccessTokenInterface;
use RuntimeException;

class AccessToken extends Model implements AccessTokenInterface, ResourceOwnerAccessTokenInterface
{
    use HasFactory;

    protected $table = 'access_tokens';

    protected $fillable = [
        'access_token',
        'expires',
        'refresh_token',
        'resourceOwnerId'
    ];

    public function getToken()
    {
        return '';
    }

    public function getRefreshToken()
    {
        return '';
    }

    public function getExpires()
    {
        return '';
    }

    public function hasExpired()
    {
        // TODO: Implement hasExpired() method.
    }

    public function getValues()
    {
        return [
            'token_type' => "Bearer"
        ];
    }

    public function getResourceOwnerId()
    {
        // TODO: Implement getResourceOwnerId() method.
    }
}
