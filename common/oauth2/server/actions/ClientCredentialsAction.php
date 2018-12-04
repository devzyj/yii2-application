<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use yii\web\BadRequestHttpException;

use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Exception\OAuthServerException;
use GuzzleHttp\Psr7\ServerRequest;

use common\oauth2\server\repositories\AccessTokenRepositoryInterface;
use common\oauth2\server\repositories\ClientRepositoryInterface;
use common\oauth2\server\repositories\ScopeRepositoryInterface;

/**
 * ClientCredentialsAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsAction extends Action
{
    const GRANT_TYPE = 'client_credentials';
    
    public $accessTokenRepositoryClass;
    public $clientRepositoryClass;
    public $scopeRepositoryClass;
    
    /**
     * Generate credentials.
     * 
     * @return array
     */
    public function run()
    {
        $request = Yii::$app->getRequest();
        
        // 获取 `client_id` 和 `client_secret`。
        list ($identifier, $secret) = $this->getClientAuthCredentials($request);
        
        // 获取客户端实例。
        $client = $this->getClient($identifier);
        
        // 验证客户端密钥。
        $this->validateClientSecret($client, $secret);
        
        // 验证客户端授权类型。
        $this->validateClientGrantType($client, self::GRANT_TYPE);
        
        // 获取并且确认请求中的权限。
        $scopes = $this->getScopes($request);
        
        // 确定最终授权的权限列表。
        $finalizedScopes = $this->getScopeRepository()->finalize($scopes, self::GRANT_TYPE, $client);
        
        // 创建访问令牌。
        $accessToken = $this->generateAccessToken($client->getAccessTokenDuration(), $client, $finalizedScopes);
        
        // 生成并返回认证信息。
        return $this->generateCredentials($scopes, $accessToken);
        
        
        
        
        
        
        
        
        
        /* @var $accessTokenRepository AccessTokenRepositoryInterface */
        $accessTokenRepository = Yii::createObject($this->accessTokenRepositoryClass);
        /* @var $clientRepository ClientRepositoryInterface */
        $clientRepository = Yii::createObject($this->clientRepositoryClass);
        /* @var $scopeRepository ScopeRepositoryInterface */
        $scopeRepository = Yii::createObject($this->scopeRepositoryClass);
        
        
        $clientRepository->getEntity($identifier, $grantType, $secret)
        
        
        
        
        
        
        $privateKey = new CryptKey(dirname(__DIR__) . '/keys/private.key', null, false);
        $encryptionKey = 'lxZFUEsBCJ2Yb14IF2ygAHI5N4+ZAUXXaSeeJm6+twsUmIen';
        
        // Setup the authorization server
        $server = new AuthorizationServer(
            $clientRepository,
            $accessTokenRepository,
            $scopeRepository,
            $privateKey,
            $encryptionKey
        );
        
        // Enable the client credentials grant on the server
        $server->enableGrantType(
            new ClientCredentialsGrant(),
            new \DateInterval('PT1H') // access tokens will expire after 1 hour
        );
        
        $request = Yii::$app->getRequest();
        $response = Yii::$app->getResponse();
        $request = new ServerRequest();
        
        try {
            // Try to respond to the request
            return $server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // All instances of OAuthServerException can be formatted into a HTTP response
            return $exception->generateHttpResponse($response);
        } catch (\Exception $exception) {
            throw $exception;
            // Unknown exception
            /*$body = new \Stream('php://temp', 'r+');
            $body->write($exception->getMessage());
            return $response->withStatus(500)->withBody($body);*/
        
        }
    }
}