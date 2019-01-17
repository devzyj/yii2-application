<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\models;

use Yii;

/**
 * This is the model class for table "{{%oauth_client}}".
 *
 * @property int $id ID
 * @property string $name 名称
 * @property string $identifier 标识
 * @property string $secret 密钥
 * @property string $grant_types 授权类型
 * @property string $redirect_uri 回调地址
 * @property int $access_token_duration 访问令牌的持续时间
 * @property int $refresh_token_duration 更新令牌的持续时间
 *
 * @property OauthClientScope[] $oauthClientScopes 客户端与权限的关联关系
 * @property OauthScope[] $oauthScopes 客户端的权限
 * 
 * @property array $grantTypes 客户端的授权类型
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClient extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oauth_client}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'identifier', 'secret', 'grant_types', 'redirect_uri', 'access_token_duration', 'refresh_token_duration'], 'required'],
            [['access_token_duration', 'refresh_token_duration'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['identifier'], 'string', 'max' => 20],
            [['secret'], 'string', 'max' => 32],
            [['grant_types'], 'string', 'max' => 100],
            [['redirect_uri'], 'string', 'max' => 255],
            [['name'], 'unique'],
            [['identifier'], 'unique'],
            [['secret'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'identifier' => 'Identifier',
            'secret' => 'Secret',
            'grant_types' => 'Grant Types',
            'redirect_uri' => 'Redirect Uri',
            'access_token_duration' => 'Access Token Duration',
            'refresh_token_duration' => 'Refresh Token Duration',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClientScopes()
    {
        return $this->hasMany(OauthClientScope::className(), ['client_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOauthScopes()
    {
        return $this->hasMany(OauthScope::className(), ['id' => 'scope_id'])->viaTable(OauthClientScope::tableName(), ['client_id' => 'id']);
    }
    
    /**
     * 通过客户端标识，查询并返回一个客户端模型。
     * 
     * @param string $identifier 客户端标识。
     * @return static|null 客户端模型实例，如果没有匹配到，则为 `null`。
     */
    public static function findOneByIdentifier($identifier)
    {
        return static::findOne(['identifier' => $identifier]);
    }

    /**
     * 获取客户端的授权类型。
     *
     * @return string[]
     */
    public function getGrantTypes()
    {
        $grantTypes = trim($this->grant_types);
        if ($grantTypes) {
            return explode(' ', $grantTypes);
        }
    
        return [];
    }
}