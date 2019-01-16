<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\yii2\oauth2\server\actions;

use Yii;
use devjerry\yii2\oauth2\server\models\AuthorizationForm;
use devzyj\oauth2\server\interfaces\ScopeEntityInterface;

/**
 * LoginAction class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class LoginAction extends \yii\base\Action
{
    /**
     * @var string 布局文件。
     */
    public $layout = 'main';
    
    /**
     * @var string 视图文件。
     */
    public $view = 'login';
    
    /**
     * 授权用户登录。
     */
    public function run()
    {
        /* @var $model AuthorizationForm */
        $model = Yii::createObject(AuthorizationForm::class);

        /* @var $module \devjerry\yii2\oauth2\server\Module */
        $module = $this->controller->module;
        
        // 授权请求。
        $authorizeRequest = $module->getAuthorizeRequest();
        
        // 设置请求的权限。
        $model->scopes = array_map(function (ScopeEntityInterface $scopeEntity) {
            return $scopeEntity->getIdentifier();
        }, $authorizeRequest->getScopeEntities());
        
        // 获取授权用户。
        $user = $module->getUser();
        
        // 处理提交后的数据。
        if ($model->load(Yii::$app->request->post()) && $model->login($user)) {
            return $this->controller->redirect($user->getReturnUrl());
        }

        // 显示登录页面。
        $model->password = '';
        $this->controller->layout = $this->layout;
        return $this->controller->render($this->view, [
            'model' => $model,
            'client' => $authorizeRequest->getClientEntity(),
            'scopes' => $authorizeRequest->getScopeEntities(),
        ]);
    }
}