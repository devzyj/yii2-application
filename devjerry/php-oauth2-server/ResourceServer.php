<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server;

use devjerry\oauth2\server\base\BaseObject;
use devjerry\oauth2\server\interfaces\ServerRequestInterface;
use devjerry\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\validators\AuthorizationValidator;
use devjerry\oauth2\server\validators\AuthorizationValidatorInterface;
use devjerry\oauth2\server\exceptions\InvalidAccessTokenException;

/**
 * ResourceServer class.
 * 
 * ```php
 * use devjerry\oauth2\server\ResourceServer;
 * use devjerry\oauth2\server\exceptions\BadRequestException;
 * use devjerry\oauth2\server\exceptions\InvalidAccessTokenException;
 * 
 * // 实例化对像。
 * $resourceServer = new ResourceServer([
 *     'accessTokenRepository' => new AccessTokenRepository(),
 *     'accessTokenCryptKey' => [
 *         'publicKey' => '/path/to/publicKey' // 公钥路径。
 *     ],
 *     //'accessTokenCryptKey' => 'string key', // 字符串密钥。
 *     //'accessTokenQueryParam' => 'access-token', // 只在 [[validateServerRequest()]] 中有效。
 * ]);
 * 
 * // 调用方法一：
 * try {
 *     // 验证请求。
 *     $accessTokenEntity = $resourceServer->validateServerRequest(ServerRequestInterface $request);
 * } catch (BadRequestException $exception) {
 *     // 请求中缺少访问令牌参数。
 *     
 * } catch (InvalidAccessTokenException $exception) {
 *     // 无效的访问令牌。
 *     
 * }
 * 
 * // 或者方法二：
 * try {
 *     // 已获取到的访问令牌。
 *     $strAccessToken = '';
 *     
 *     // 验证访问令牌。
 *     $accessTokenEntity = $resourceServer->validateAccessToken($strAccessToken);
 * } catch (InvalidAccessTokenException $exception) {
 *     // 无效的访问令牌。
 *     
 * }
 * ```
 * 
 * @property AccessTokenRepositoryInterface $accessTokenRepository 访问令牌存储库。
 * @property mixed $accessTokenCryptKey 访问令牌密钥。
 * @property string $accessTokenQueryParam 访问令牌在地址查询参数中的名称。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ResourceServer extends BaseObject
{
    /**
     * @var AccessTokenRepositoryInterface 访问令牌存储库。
     */
    private $_accessTokenRepository;
    
    /**
     * @var mixed 访问令牌密钥。
     */
    private $_accessTokenCryptKey;
    
    /**
     * @var string 访问令牌在地址查询参数中的名称。
     */
    private $_accessTokenQueryParam;

    /**
     * @var AuthorizationValidatorInterface
     */
    private $_validator;
    
    /**
     * 获取访问令牌存储库。
     *
     * @return AccessTokenRepositoryInterface
     */
    public function getAccessTokenRepository()
    {
        return $this->_accessTokenRepository;
    }
    
    /**
     * 设置访问令牌存储库。
     *
     * @param AccessTokenRepositoryInterface $accessTokenRepository
     */
    public function setAccessTokenRepository(AccessTokenRepositoryInterface $accessTokenRepository)
    {
        $this->_accessTokenRepository = $accessTokenRepository;
    }
    
    /**
     * 获取访问令牌密钥。
     *
     * @return mixed
     */
    public function getAccessTokenCryptKey()
    {
        return $this->_accessTokenCryptKey;
    }
    
    /**
     * 设置访问令牌密钥。
     *
     * @param mixed $key
     */
    public function setAccessTokenCryptKey($key)
    {
        $this->_accessTokenCryptKey = $key;
    }

    /**
     * 获取访问令牌在地址查询参数中的名称。
     *
     * @return string
     */
    public function getAccessTokenQueryParam()
    {
        return $this->_accessTokenQueryParam;
    }
    
    /**
     * 设置访问令牌在地址查询参数中的名称。
     *
     * @param string $name
     */
    public function setAccessTokenQueryParam($name)
    {
        $this->_accessTokenQueryParam = $name;
    }
    
    /**
     * 获取授权验证器。
     *
     * @return AuthorizationValidatorInterface
     */
    public function getValidator()
    {
        if ($this->_validator === null) {
            $this->_validator = new AuthorizationValidator([
                'accessTokenRepository' => $this->getAccessTokenRepository(),
                'accessTokenCryptKey' => $this->getAccessTokenCryptKey(),
                'accessTokenQueryParam' => $this->getAccessTokenQueryParam(),
            ]);
        }
    
        return $this->_validator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    
        if ($this->getAccessTokenRepository() === null) {
            throw new \LogicException('The `accessTokenRepository` property must be set.');
        }
    }
    
    /**
     * 验证服务器请求的认证信息。
     * 
     * @param ServerRequestInterface $request 服务器请求。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     */
    public function validateServerRequest($request)
    {
        return $this->getValidator()->validateServerRequest($request);
    }

    /**
     * 验证访问令牌。
     *
     * @param string $accessToken 访问令牌。
     * @return AccessTokenEntityInterface 访问令牌实例。
     * @throws InvalidAccessTokenException 访问令牌无效。
     */
    public function validateAccessToken($accessToken)
    {
        return $this->getValidator()->validateAccessToken($accessToken);
    }
}