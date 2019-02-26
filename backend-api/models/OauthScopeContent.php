<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%oauth_scope_content}}".
 *
 * @property int $scope_id 权限 ID
 * @property string $allowed_apis 允许访问的 APIs
 *
 * @property OauthScope $oauthScope 权限
 * 
 * @property array $allowedAPIs 允许访问的 APIs
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthScopeContent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oauth_scope_content}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_backend');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scope_id'], 'required'],
            [['scope_id'], 'integer'],
            [['allowed_apis'], 'string', 'max' => 2000],
            [['scope_id'], 'unique'],
            [['scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => OauthScope::class, 'targetAttribute' => ['scope_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'scope_id' => 'Scope ID',
            'allowed_apis' => 'Allowed APIs',
        ];
    }

    /**
     * 获取权限。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getScope()
    {
        return $this->hasOne(OauthScope::class, ['id' => 'scope_id']);
    }

    /**
     * 获取允许访问的 APIs。
     *
     * @return array
     */
    public function getAllowedAPIs()
    {
        $allowedAPIs = trim($this->allowed_apis);
        if ($allowedAPIs) {
            return explode(' ', $allowedAPIs);
        }
    
        return [];
    }
    
    /**
     * 确保 APIs 格式正确。
     *
     * 1. 不处理单个字符 `*`;
     * 2. 字符串中如果有字符 `*`，则去除未尾的 `/`，并且在开头添加 '/'。
     * 3. 字符串中如果没有字符 `*`，则在开头和未尾都添加 `/`。
     *
     * 返回值的格式可能包括以下几种：
     * [
     *     '*',
     *     '/api/user-*',
     *     '/api/users/*',
     *     '/api/users/create/'
     * ]
     *
     * @param array $list API列表。
     * @return array
     */
    public function ensureAllowedAPIs($list)
    {
        foreach ($list as $key => $value) {
            if ($value === '*') {
                $list[$key] = $value;
                continue;
            }
    
            $value = '/' . trim($value, '/');
            if (strpos($value, '*') === false) {
                $value .= '/';
            }
    
            $list[$key] = $value;
        }
    
        return $list;
    }
    
    /**
     * 检查 API 是否被允许。
     *
     * @param string $api 需要检查的 API。
     * @return boolean
     */
    public function checkAllowedApi($api)
    {
        $api = '/' . trim($api, '/') . '/';
        $list = $this->ensureAllowedAPIs($this->getAllowedAPIs());
        foreach ($list as $allowed) {
            if ($allowed === '*' || $allowed === $api || (($pos = strpos($allowed, '*')) !== false && !strncmp($api, $allowed, $pos))) {
                return true;
            }
        }
    
        return false;
    }
}
