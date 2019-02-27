<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace backendApi\controllers\demo;

use Yii;
use yii\helpers\Url;
use yii\helpers\Html;

/**
 * OAuth controller.
 */
class OauthController extends \yii\web\Controller
{
    /**
     * 生成 `code_verifier`
     * 
     * @param string $data
     * @return string
     */
    public static function generateCodeVerifier($data = null)
    {
        if ($data === null) {
            $data = random_bytes(32);
        }
        
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * 生成 `code_challenge`
     * 
     * @param string $codeVerifier
     * @param string $method 生成方法，可以是 `S256`，`plain`。
     * @return string
     */
    public static function generateCodeChallenge($codeVerifier, $method = 'S256')
    {
        if ($method === 'S256') {
            return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
        }
        
        return $codeVerifier;
    }
    
    public function actionIndex()
    {
        $codeChallengeMethod = 'plain'; // `plain` or `S256`
        $codeVerifier = static::generateCodeVerifier();
        
        $route = [
            '/oauth2/authorize',
            'response_type' => '',
            'client_id' => 'f4c22926e400ebca',
            'scope' => 'any',
            'state' => 'abc123',
            'code_challenge_method' => $codeChallengeMethod,
            'code_challenge' => static::generateCodeChallenge($codeVerifier, $codeChallengeMethod),
            'redirect_uri' => Url::toRoute('callback', true),
        ];
        
        $route['response_type'] = 'code';
        $content[] = Html::a('授权码模式', $route, ['target' => '_blank']);
        
        $route['response_type'] = 'token';
        $content[] = Html::a('简化模式', $route, ['target' => '_blank']);
        
        $content[] = "CodeVerifier: {$codeVerifier}";
        return implode(' | ', $content);
    }
    
    public function actionCallback()
    {
        print_r(Yii::$app->getRequest()->getQueryParams());
    }
}
