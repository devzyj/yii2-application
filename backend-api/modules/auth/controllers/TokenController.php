<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiAuth\controllers;

use Yii;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use backendApi\models\Admin;
use yii\helpers\ArrayHelper;

/**
 * TokenController class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class TokenController extends \backendApiAuth\components\ApiController
{
    /**
     * @var \backendApiAuth\Module 授权模块。
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
     */
    public function actionIndex()
    {
        if (!($user = Yii::$app->getUser()) || !($client = $user->getIdentity(false))) {
            throw new ServerErrorHttpException('Client must be logged in.');
        }

        $request = Yii::$app->getRequest();
        
        // 获取请求的权限范围。
        $scope = $request->getBodyParam('scope');
        
        // TODO 获取有效的权限范围。
        
        
        // 执行不同的授权。
        $grantType = $request->getBodyParam('grant_type');
        if ($grantType === 'password') {
            return $this->password($client, $scope);
        } elseif ($grantType === 'client_credentials') {
            return $this->clientCredentials($client, $scope);
        } elseif ($grantType === 'refresh_token') {
            return $this->refreshToken($client, $scope);
        }
        
        throw new BadRequestHttpException('The grant type is unauthorized for this client.');
    }

    /**
     * 用户密码授权。
     *
     * @param \backendApi\models\Client $client 客户端模型。
     * @param string $scope 权限范围。
     * @return array
     */
    protected function password($client, $scope)
    {
        $request = Yii::$app->getRequest();
        $username = $request->getBodyParam('username');
        $password = $request->getBodyParam('password');
        if (!$username || !$password) {
            throw new BadRequestHttpException('Username or password cannot be blank.');
        }
        
        /* @var $model Admin */
        $model = Admin::findOneByUsername($username);
        if (!$model || !$model->validatePassword($password)) {
            throw new BadRequestHttpException('Username or password is error.');
        } elseif (!$model->getIsValid()) {
            throw new ForbiddenHttpException('User is invalid.');
        }
        
        $clientId = $client->getPrimaryKey();
        $userId = $model->getPrimaryKey();
        $expiresIn = $client->access_token_duration;
        $refreshExpiresIn = $client->refresh_token_duration;
        
        // 生成并返回令牌。
        return $this->module->getToken()->generateUserCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope);
    }
    
    /**
     * 客户端授权。
     * 
     * @param \backendApi\models\Client $client 客户端模型。
     * @param string $scope 权限范围。
     * @return array
     */
    protected function clientCredentials($client, $scope)
    {
        $clientId = $client->getPrimaryKey();
        $expiresIn = $client->access_token_duration;
        $refreshExpiresIn = $client->refresh_token_duration;
        
        // 生成并返回令牌。
        return $this->module->getToken()->generateClientCredentials($clientId, $expiresIn, $refreshExpiresIn, $scope);
    }

    /**
     * 更新令牌。
     *
     * @param \backendApi\models\Client $client 客户端模型。
     * @param string $scope 权限范围。
     * @return array
     */
    protected function refreshToken($client, $scope)
    {
        $request = Yii::$app->getRequest();
        $refreshToken = $request->getBodyParam('refresh_token');
        if (!$refreshToken) {
            throw new BadRequestHttpException('Refresh token cannot be blank.');
        }
        
        $token = $this->module->getToken();
        $refreshToken = $token->getRefreshTokenData($refreshToken);
        if (!$refreshToken) {
            throw new BadRequestHttpException('Refresh token is invalid.');
        }

        $clientId = $client->getPrimaryKey();
        $userId = ArrayHelper::getValue($refreshToken, 'user_id');
        $expiresIn = $client->access_token_duration;
        $refreshExpiresIn = $client->refresh_token_duration;

        // 更新并返回令牌。
        return $this->module->getToken()->refreshCredentials($clientId, $userId, $expiresIn, $refreshExpiresIn, $scope);
    }
}
