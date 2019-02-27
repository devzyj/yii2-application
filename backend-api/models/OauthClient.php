<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\AttributesBehavior;
use devzyj\yii2\oauth2\server\models\OauthClient as DevzyjOauthClient;

/**
 * This is the model class for table "{{%oauth_client}}".
 *
 * @property OauthClientScope[] $oauthClientScopes 客户端与权限的关联关系
 * @property OauthScope[] $oauthScopes 客户端的权限
 * @property OauthScope[] $defaultOauthScopes 客户端的默认权限
 * @property OauthClientSetting $oauthClientSetting 客户端配置
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClient extends DevzyjOauthClient
{
    /**
     * {@inheritdoc}
     */
    public static function getDb()
    {
        return Yii::$app->get('db_backend');
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'attributesBehavior' => [
                'class' => AttributesBehavior::class,
                'preserveNonEmptyValues' => true,
                'attributes' => [
                    'identifier' => [
                        self::EVENT_BEFORE_INSERT => $fn = [static::class, 'generateIdentifier'],
                        self::EVENT_BEFORE_UPDATE => $fn,
                    ],
                    'secret' => [
                        self::EVENT_BEFORE_INSERT => $fn = [static::class, 'generateSecret'],
                        self::EVENT_BEFORE_UPDATE => $fn,
                    ],
                ],
            ],
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getOauthClientScopes()
    {
        return $this->hasMany(OauthClientScope::class, ['client_id' => 'id']);
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
     * 获取客户端配置查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClientSetting()
    {
        return $this->hasOne(OauthClientSetting::class, ['client_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findOneByIdentifier($identifier)
    {
        return static::findOne(['identifier' => $identifier]);
    }

    /**
     * 生成客户端标识。
     *
     * @return string
     */
    public static function generateIdentifier()
    {
        return substr(md5(microtime().rand(1000, 9999)), 8, 16);
    }
    
    /**
     * 生成客户端密钥。
     *
     * @return string
     */
    public static function generateSecret()
    {
        return md5(microtime().rand(1000, 9999));
    }
    
    /**
     * 检查  IP 是否被允许。
     * 
     * @param string $ip 需要检查的IP地址。
     * @return boolean 是否允许。
     */
    public function checkAllowedIp($ip)
    {
        if ($this->oauthClientSetting && $this->oauthClientSetting->checkAllowedIp($ip)) {
            return true;
        }
    
        return false;
    }
}
