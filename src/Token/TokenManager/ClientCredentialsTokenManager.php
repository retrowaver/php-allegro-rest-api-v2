<?php
namespace Retrowaver\Allegro\REST\Token\TokenManager;

use Retrowaver\Allegro\REST\Token\ClientCredentialsToken;
use Retrowaver\Allegro\REST\Token\ClientCredentialsTokenInterface;
use Retrowaver\Allegro\REST\Token\CredentialsInterface;
use Psr\Http\Message\ResponseInterface;

class ClientCredentialsTokenManager extends BaseTokenManager implements ClientCredentialsTokenManagerInterface
{
    public function getClientCredentialsToken(
        CredentialsInterface $credentials
    ): ClientCredentialsTokenInterface {
        $request = $this->messageFactory->createRequest(
            'POST',
            $this->getClientCredentialsTokenUri(),
            $this->getBasicAuthHeader($credentials)
        );

        $response = $this->client->sendRequest($request);

        $this->validateGetTokenResponse($request, $response, ['access_token', 'expires_in']);
        return $this->createClientCredentialsTokenFromResponse($response);
    }

    protected function getClientCredentialsTokenUri(): string
    {
        return static::TOKEN_URI . "?" . http_build_query([
            'grant_type' => 'client_credentials'
        ]);
    }

    protected function createClientCredentialsTokenFromResponse(
        ResponseInterface $response
    ): ClientCredentialsTokenInterface {
        $decoded = json_decode((string)$response->getBody());
        return new ClientCredentialsToken($decoded->access_token, $decoded->expires_in);
    }
}
