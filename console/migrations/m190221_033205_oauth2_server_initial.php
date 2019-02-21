<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

use yii\db\Migration;

/**
 * Class m190221_033205_oauth2_server_initial
 */
class m190221_033205_oauth2_server_initial extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%oauth_client}}', [
            'identifier' => 'f4c22926e400ebca',
            'secret' => '692569f364854bc130687297c770c2c0',
            'redirect_uri' => 'http://backend.application.yii2.devzyj.zyj/test/oauth-callback',
        ], [
            'id' => 1
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
    }
}
