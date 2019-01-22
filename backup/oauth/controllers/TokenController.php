<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauth\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\ForbiddenHttpException;
use backendApi\models\OauthScope;
use backendApi\models\Admin as OauthUser;

/**
 * TokenController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class TokenController extends \backendApiOauth\components\ApiController
{
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    const GRANT_TYPE_PASSWORD = 'password';
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
    
    /**
     * @var \backendApiOauth\Module 授权模块。
     */
    public $module;
    
    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['POST'],
        ];
    }
    
    /**
     * 授权入口。
     * 
     * @return array 认证信息。
     */
    public function actionIndex()
    {
        /* @var $client \backendApi\models\OauthClient */
        if (!($user = Yii::$app->getUser()) || !($client = $user->getIdentity(false))) {
            throw new ServerErrorHttpException('Invalid client.');
        }

        // 获取授权类型。
        $grantType = $this->getGrantType($client);

        // 获取请求的权限范围。
        $scope = Yii::$app->getRequest()->getBodyParam('scope');
        
        // 创建认证信息。
        return $this->createCredentials($client, $grantType, $scope);
    }
    
    /**
     * 获取授权类型。
     * 
     * @param \backendApi\models\OauthClient $client 客户端模型。
     * @return string
     */
    protected function getGrantType($client)
    {
        $request = Yii::$app->getRequest();
        
        // 获取授权类型。
        $grantType = $request->getBodyParam('grant_type');
        if (empty($grantType)) {
            throw new BadRequestHttpException('Missing parameter: "grant_type" required.');
        }
        
        // 检查授权类型是否被允许。
        $grantTypes = $client->getGrantTypes();
        if (!in_array($grantType, $grantTypes, true)) {
            throw new BadRequestHttpException('The grant type is unauthorized for this client.');
        }
        
        return $grantType;
    }
    
    /**
     * 创建认证信息。
     * 
     * @param \backendApi\models\OauthClient $client 客户端模型。
     * @param string $grantType 授权类型。
     * @param string $scope 权限范围。
     * @return array
     */
    protected function createCredentials($client, $grantType, $scope)
    {
        // 执行不同的授权。
        if ($grantType === self::GRANT_TYPE_CLIENT_CREDENTIALS) {
            return $this->clientCredentials($client, $scope);
        } elseif ($grantType === self::GRANT_TYPE_PASSWORD) {
            return $this->password($client, $scope);
        } elseif ($grantType === self::GRANT_TYPE_REFRESH_TOKEN) {
            return $this->refreshToken($client, $scope);
        }
        
        throw new BadRequestHttpException('Unsupported grant type.');
    }
    
    /**
     * 客户端授权。
     * 
     * @param \backendApi\models\OauthClient $client 客户端模型。
     * @param string $scope 权限范围。
     * @return array 认证信息。
     * @throws \yii\web\BadRequestHttpException 指定的权限无效。
     */
    protected function clientCredentials($client, $scope)
    {
        $clientId = $client->identifier;
        $expiresIn = $client->access_token_duration;
        $refreshExpiresIn = $client->refresh_token_duration;
        $scope = $this->ensureClientScopes($client, $scope);
        
        // 生成并返回令牌。
        return $this->module->getToken()->generateClientCredentials($clientId, $expiresIn, $refreshExpiresIn, $scope);
    }
    
    /**
     * 确认客户端权限。
     * 
     * 1. 首先获取分配给客户端的权限，如果没有分配的权限，则获取所有权限；
     * 2. 如果有指定 `$scope`，则比对获取到的权限。如果没有指定 `$scope`，则直接使用获取到的权限；
     * 
     * @param \backendApi\models\OauthClient $client 客户端模型。
     * @param string $scope 权限范围。
     * @return string
     */
    protected function ensureClientScopes($client, $scope)
    {
        // 获取客户端权限。
        $scopes = $client->getScopes()->select('scope')->column();
        if (empty($scopes)) {
            // 获取全部权限。
            $scopes = OauthScope::find()->select('scope')->column();
        }
        
        if ($scope) {
            // 指定了申请权限时，确认权限是有效的。
            $applyScopes = explode(' ', $scope);
            foreach ($applyScopes as $apply) {
                if (!in_array($apply, $scopes)) {
                    throw new BadRequestHttpException('The scope requested is invalid for this client.');
                }
            }
        } else {
            // 没有指定申请权限时，使用获取到的客户端权限。
            $scope = $scopes ? implode(' ', $scopes) : null;
        }
        
        return $scope;
    }

    /**
     * 用户密码授权。
     *
     * @param \backendApi\models\OauthClient $client 客户端模型。
     * @param string $scope 权限范围。
     * @return array
     */
    protected function password($client, $scope)
    {
        $request = Yii::$app->getRequest();
        $username = $request->getBodyParam('username');
        $password = $request->getBodyParam('password');
        if (!$username || !$password) {
            throw new BadRequestHttpException('Missing parameter: "username" and "password" required.');
        }
        
        /* @var $model OauthUser */
        $model = OauthUser::findOneByUsername($username);
        if (!$model || !$model->validatePassword($password)) {
            throw new UnauthorizedHttpException('Invalid username or password.');
        } elseif (!$model->getIsValid()) {
            throw new ForbiddenHttpException('Invalid user.');
        }

        $userId = $model->getPrimaryKey();
        $scope = $this->ensureUserScopes($model, $scope);
        
        $clientId = $client->identifier;
        $expiresIn = $client->access_token_duration;
        $refreshExpiresIn = $client->refresh_token_duration;
        
        // 生成并返回令牌。
        return $this->module->getToken()->generateUserCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope);
    }
    
    /**
     * 确认用户权限。
     * 
     * @param OauthUser $user 用户模型。
     * @param string $scope 权限范围。
     * @return string
     */
    protected function ensureUserScopes($user, $scope)
    {
        // TODO throw new BadRequestHttpException('The scope requested is invalid for this user.');
        return $scope;
    }

    /**
     * 更新令牌。
     *
     * @param \backendApi\models\OauthClient $client 客户端模型。
     * @param string $scope 权限范围。
     * @return array
     */
    protected function refreshToken($client, $scope)
    {
        $request = Yii::$app->getRequest();
        $refreshToken = $request->getBodyParam('refresh_token');
        if (!$refreshToken) {
            throw new BadRequestHttpException('Missing parameter: "refresh_token" required.');
        }
        
        $token = $this->module->getToken();
        $refreshToken = $token->getRefreshTokenData($refreshToken);
        if (!$refreshToken) {
            throw new BadRequestHttpException('Invalid refresh token.');
        }

        $clientId = $client->identifier;
        $expiresIn = $client->access_token_duration;
        $refreshExpiresIn = $client->refresh_token_duration;
        $userId = ArrayHelper::getValue($refreshToken, 'user_id');
        if ($userId === null) {
            $scope = $this->ensureClientScopes($client, $scope);
        } else {
            $user = OauthUser::findOne($userId);
            $scope = $this->ensureUserScopes($user, $scope);
        }
        
        // 确认申请的权限是否超出上一次申请的范围。
        $refreshScope = ArrayHelper::getValue($refreshToken, 'scope');
        if ($refreshScope) {
            $refreshScopes = explode(' ', $refreshScope);
            $scopes = explode(' ', $scope);
            foreach ($scopes as $apply) {
                if (!in_array($apply, $refreshScopes)) {
                    throw new BadRequestHttpException('The scope requested is invalid for this request.');
                }
            }
        }
        
        // 更新并返回令牌。
        return $this->module->getToken()->refreshCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope);
    }
}
