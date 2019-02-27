<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\models;

use Yii;

/**
 * This is the model class for table "{{%oauth_client_setting}}".
 *
 * @property int $client_id 客户端 ID
 * @property int $admin_id 管理员 ID
 * @property string $allowed_ips 允许访问的 IPs
 *
 * @property Admin $admin 管理员
 * @property OauthClient $oauthClient 客户端
 * 
 * @property array $allowedIPs 允许访问的 IPs
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class OauthClientSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%oauth_client_setting}}';
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
            [['client_id', 'admin_id'], 'required'],
            [['client_id', 'admin_id'], 'integer'],
            [['allowed_ips'], 'string', 'max' => 255],
            [['client_id'], 'unique'],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admin::class, 'targetAttribute' => ['admin_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => OauthClient::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'client_id' => 'Client ID',
            'admin_id' => 'Admin ID',
            'allowed_ips' => 'Allowed Ips',
        ];
    }

    /**
     * 获取管理员查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admin::class, ['id' => 'admin_id']);
    }

    /**
     * 获取客户端查询对像。
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getOauthClient()
    {
        return $this->hasOne(OauthClient::class, ['id' => 'client_id']);
    }

    /**
     * 获取客户端允许访问的 IPs。
     *
     * @return array
     */
    public function getAllowedIPs()
    {
        $allowedIPs = trim($this->allowed_ips);
        if ($allowedIPs) {
            return explode(' ', $allowedIPs);
        }
    
        return [];
    }
    
    /**
     * 检查 IP 是否被允许。
     *
     * @param string $ip 需要检查的IP地址。
     * @return boolean
     */
    public function checkAllowedIp($ip)
    {
        foreach ($this->getAllowedIPs() as $allowed) {
            if ($allowed === '*' || $allowed === $ip || (($pos = strpos($allowed, '*')) !== false && !strncmp($ip, $allowed, $pos))) {
                return true;
            }
        }
    
        return false;
    }
}
