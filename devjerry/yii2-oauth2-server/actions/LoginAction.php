<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use yii\base\InvalidConfigException;
use devzyj\oauth2\server\interfaces\ClientEntityInterface;
use devzyj\oauth2\server\interfaces\ScopeEntityInterface;
use devjerry\yii2\oauth2\server\repositories\ClientRepository;
use devjerry\yii2\oauth2\server\repositories\ScopeRepository;
use devjerry\yii2\oauth2\server\interfaces\OAuthLoginFormInterface;

/**
 * LoginAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class LoginAction extends \yii\base\Action
{
    /**
     * @var OAuthLoginFormInterface 表单模型类名。
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
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ($this->modelClass === null) {
            throw new InvalidConfigException('The `modelClass` property must be set.');
        }
        
        if ($this->view === null) {
            $this->view = 'login';
            $this->layout = false;
        }
    }
    
    /**
     * 授权用户登录。
     */
    public function run()
    {
        // 获取请求参数。
        $request = Yii::$app->getRequest();
        $clientId = $request->getQueryParam('client_id');
        $scope = $request->getQueryParam('scope');
        $scopes = $scope ? explode(' ', $scope) : [];
        $returnUrl = $request->getQueryParam('return_url');
        $referrerUrl = $request->getQueryParam('referrer_url');
        
        /* @var $model OAuthLoginFormInterface */
        $model = Yii::createObject($this->modelClass);

        // 设置请求的默认权限。
        $model->setDefaultScopes($scopes);

        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        
        // 处理提交后的数据。
        if ($model->load($request->post()) && $model->login($module->getUser())) {
            return $this->controller->redirect($returnUrl);
        }
        
        // 显示登录页面。
        $this->controller->layout = $this->layout;
        return $this->controller->render($this->view, [
            'model' => $model,
            'clientEntity' => $this->getClientEntity($clientId),
            'scopeEntities' => $this->getScopeEntities($scopes),
        ]);
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
                $scopeEntity = $repository->getScopeEntity($scope);
                if ($scopeEntity) {
                    $result[$scope] = $scopeEntity;
                }
            }
        }
        
        return array_values($result);
    }
}