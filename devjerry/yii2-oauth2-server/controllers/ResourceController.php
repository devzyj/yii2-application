<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\controllers;

use Yii;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\web\HttpException;
use devzyj\oauth2\server\ResourceServer;
use devzyj\oauth2\server\interfaces\AccessTokenEntityInterface;
use devzyj\oauth2\server\exceptions\OAuthServerException;

/**
 * ResourceController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ResourceController extends \yii\web\Controller
{
    /**
     * @var \devjerry\yii2\oauth2\server\Module 控制器所属的模块。
     */
    public $module;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
        ];
    }
    
    /**
     * 用于远程验证访问令牌。
     * 
     * @return array 访问令牌的内容。
     * @throws HttpException 缺少参数，或者无效的访问令牌。
     */
    public function actionIndex()
    {
        return $this->validateServerRequest();
    }
    
    /**
     * 验证服务器请求的认证信息。
     * 
     * @return array 访问令牌的内容。
     * @throws HttpException 缺少参数，或者无效的访问令牌。
     */
    public function validateServerRequest()
    {
        $resourceServer = $this->createResourceServer();
        
        try {
            $accessToken = $resourceServer->validateServerRequest($this->getServerRequest());
            return $this->makeAccessTokenData($accessToken);
        } catch (OAuthServerException $e) {
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * 验证访问令牌。
     *
     * @param string $accessToken 访问令牌。
     * @return array 访问令牌的内容。
     * @throws HttpException 无效的访问令牌。
     */
    public function validateAccessToken($accessToken)
    {
        $resourceServer = $this->createResourceServer();

        try {
            $accessToken = $resourceServer->validateAccessToken($accessToken);
            return $this->makeAccessTokenData($accessToken);
        } catch (OAuthServerException $e) {
            throw new HttpException($e->getHttpStatusCode(), $e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * @param AccessTokenEntityInterface $accessToken 访问令牌实例。
     * @return array 显示的访问令牌内容。
     */
    protected function makeAccessTokenData($accessToken)
    {
        return [
            'access_token_id' => $accessToken->getIdentifier(),
            'client_id' => $accessToken->getClientIdentifier(),
            'user_id' => $accessToken->getUserIdentifier(),
            'scopes' => $accessToken->getScopeIdentifiers(),
        ];
    }
    
    /**
     * 创建资源服务器实例。
     * 
     * @return ResourceServer
     */
    protected function createResourceServer()
    {
        return new ResourceServer([
            'accessTokenRepository' => Yii::createObject($this->module->accessTokenRepository),
            'accessTokenCryptKey' => $this->module->accessTokenCryptKey,
            'accessTokenQueryParam' => $this->module->accessTokenQueryParam,
        ]);
    }
    
    /**
     * 获取服务器请求实例。
     * 
     * @return \yii\web\Request
     */
    protected function getServerRequest()
    {
        return Yii::createObject($this->module->serverRequest);
    }
}
