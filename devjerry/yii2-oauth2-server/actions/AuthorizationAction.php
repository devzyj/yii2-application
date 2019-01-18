<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Url;
use devzyj\oauth2\server\interfaces\ClientEntityInterface;
use devzyj\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;
use devjerry\yii2\oauth2\server\interfaces\OAuthAuthorizationFormInterface;

/**
 * AuthorizationAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationAction extends \yii\base\Action
{
    /**
     * @var string|array 授权用户的应用组件ID或配置。如果没有设置，则使用 `Yii::$app->getUser()`。
     */
    public $user;
    
    /**
     * @var OAuthAuthorizationFormInterface 表单模型类名。
     */
    public $modelClass;

    /**
     * @var string 视图文件。
     */
    public $view;
    
    /**
     * @var string 布局文件。
     */
    public $layout;

    /**
     * @var string|array 登录地址。
     */
    public $loginUrl;
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    
        if ($this->user === null) {
            throw new InvalidConfigException('The `user` property must be set.');
        } elseif ($this->modelClass === null) {
            throw new InvalidConfigException('The `modelClass` property must be set.');
        }

        if ($this->view === null) {
            $this->view = 'authorization';
            $this->layout = false;
        }
    }
    
    /**
     * 用户确认授权。
     */
    public function run()
    {
        // 获取请求参数。
        $request = Yii::$app->getRequest();
        $clientId = $request->getQueryParam('client_id');
        $scope = $request->getQueryParam('scope');
        $scopes = $scope ? explode(' ', $scope) : [];
        $returnUrl = $request->getQueryParam('return_url');

        /* @var $model OAuthAuthorizationFormInterface */
        $model = Yii::createObject($this->modelClass);

        // 处理提交后的数据。
        if ($model->load($request->post())) {
            if ($request->getIsAjax()) {
                // AJAX 数据验证。
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            } elseif ($model->authorization($this->getUser())) {
                // 授权成功。
                return $this->controller->redirect($returnUrl);
            }
        }
        
        // 显示登录页面。
        $this->controller->layout = $this->layout;
        return $this->controller->render($this->view, [
            'model' => $model,
            'clientEntity' => $this->getClientEntity($clientId),
            'scopeEntities' => $this->getScopeEntities($scopes),
            'loginUrl' => $this->makeLoginUrl(),
            'user' => $this->getUser(),
        ]);
    }
    
    /**
     * 获取授权用户。
     * 
     * @return User
     */
    public function getUser()
    {
        if ($this->user === null) {
            return Yii::$app->getUser();
        } elseif (is_string($this->user)) {
            return Yii::$app->get($this->user);
        }
        
        return Yii::createObject($this->user);
    }
    
    /**
     * 获取客户端。
     * 
     * @param string $clientId
     * @return ClientEntityInterface
     */
    protected function getClientEntity($clientId)
    {
        /* @var $repository ClientRepository */
        $repository = Yii::createObject(ClientRepository::class);
        return $repository->getClientEntityByCredentials($clientId);
    }
    
    /**
     * 获取权限。
     * 
     * @param string[] $scopes
     * @return ScopeEntityInterface[]
     */
    protected function getScopeEntities($scopes)
    {
        /* @var $repository ScopeRepository */
        $repository = Yii::createObject(ScopeRepository::class);

        $result = [];
        foreach ($scopes as $scope) {
            if (!isset($result[$scope])) {
                /* @var $scopeEntity ScopeEntityInterface */
                $scopeEntity = $repository->getScopeEntity($scope);
                if ($scopeEntity) {
                    $result[$scope] = $scopeEntity;
                }
            }
        }
        
        return array_values($result);
    }

    /**
     * 构造登录地址。
     *
     * @return string
     */
    protected function makeLoginUrl()
    {
        if ($this->loginUrl === null) {
            return '';
        }
        
        $request = Yii::$app->getRequest();
        $params['client_id'] = $request->getQueryParam('client_id');
        $params['scope'] = $request->getQueryParam('scope');
        $params['return_url'] = $request->getQueryParam('return_url');

        $url = Url::to($this->loginUrl);
        if (strpos($url, '?') === false) {
            return $url . '?' . http_build_query($params);
        } else {
            return $url . '&' . http_build_query($params);
        }
    }
}