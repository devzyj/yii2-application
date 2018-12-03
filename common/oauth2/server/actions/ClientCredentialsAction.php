<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\actions;

use Yii;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Exception\OAuthServerException;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * ClientCredentialsAction class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ClientCredentialsAction extends \yii\base\Action
{
    public $clientRepositoryClass;
    public $scopeRepositoryClass;
    public $accessTokenRepositoryClass;
    
    /**
     * Generate credentials.
     * 
     * @return array
     */
    public function run()
    {
        /* @var $clientRepository \common\oauth2\server\repositories\ClientRepository */
        $clientRepository = Yii::createObject($this->clientRepositoryClass);
        $scopeRepository = Yii::createObject($this->scopeRepositoryClass);
        $accessTokenRepository = Yii::createObject($this->accessTokenRepositoryClass);
        
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