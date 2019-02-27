<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApiOauthV1\models;

use Yii;
use backendApi\models\OauthClientScope;

/**
 * This is the model class for table "{{%oauth_client}}".
 *
 * @property OauthScope[] $oauthScopes 客户端的权限
 * @property OauthScope[] $defaultOauthScopes 客户端的默认权限
 * @property OauthClientSetting $oauthClientSetting 客户端配置
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClient extends \backendApi\models\OauthClient
{
    /**
     * @var string 新增数据的场景名称。
     */
    const SCENARIO_INSERT = 'insert';
    
    /**
     * @var string 更新数据的场景名称。
     */
    const SCENARIO_UPDATE = 'update';
    
    /**
     * @var string 重置标识的场景名称。
     */
    const SCENARIO_RESET_IDENTIFIER = 'resetIdentifier';
    
    /**
     * @var string 重置密钥的场景名称。
     */
    const SCENARIO_RESET_SECRET = 'resetSecret';
    
    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // 默认场景。
        $scenarios = parent::scenarios();
    
        // 新增数据的场景。
        $scenarios[self::SCENARIO_INSERT] = [
            'name', 'description', 'grant_types', 'redirect_uri', 'access_token_duration', 'refresh_token_duration', 'status'
        ];
    
        // 更新数据的场景。
        $scenarios[self::SCENARIO_UPDATE] = [
            'name', 'description', 'grant_types', 'redirect_uri', 'access_token_duration', 'refresh_token_duration', 'status'
        ];
    
        // 重置标识的场景。
        $scenarios[self::SCENARIO_RESET_IDENTIFIER] = [];
    
        // 重置密钥的场景。
        $scenarios[self::SCENARIO_RESET_SECRET] = [];
    
        // 返回修改后的场景列表。
        return $scenarios;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOauthScopes()
    {
        return $this->hasMany(OauthScope::class, ['id' => 'scope_id'])->viaTable(OauthClientScope::tableName(), ['client_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOauthScopes()
    {
        return $this->hasMany(OauthScope::class, ['id' => 'scope_id'])->viaTable(OauthClientScope::tableName(), ['client_id' => 'id'], function ($query) {
            $query->andWhere(['is_default' => OauthClientScope::IS_DEFAULT_YES]);
        });
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOauthClientSetting()
    {
        return $this->hasOne(OauthClientSetting::class, ['client_id' => 'id']);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @return OauthClientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OauthClientQuery(get_called_class());
    }
}
