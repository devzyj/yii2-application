<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\validators;

use devjerry\oauth2\server\interfaces\AccessTokenRepositoryInterface;
use devjerry\oauth2\server\interfaces\AccessTokenEntityInterface;
use devjerry\oauth2\server\base\ServerRequestTrait;
use devjerry\oauth2\server\exceptions\BadRequestException;
use devjerry\oauth2\server\exceptions\InvalidAccessTokenException;

/**
 * 授权信息验证器接口。
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class AuthorizationValidator implements AuthorizationValidatorInterface
{
    use ServerRequestTrait;
    
    /**
     * @var string 访问令牌在地址查询参数中的名称。
     */
    public $_accessTokenQueryParam = 'access-token';
    
    /**
     * @var AccessTokenRepositoryInterface 访问令牌存储库。
     */
    private $_accessTokenRepository;

    /**
     * @var mixed 访问令牌密钥。
     */
    private $_accessTokenCryptKey;
    
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
     * {@inheritdoc}
     * 
     * @throws BadRequestException 缺少参数。
     */
    public function validateServerRequest($request)
    {
        // 获取请求头中的授权信息。
        $authorization = $this->getRequestAuthorization($request);
        
        // 获取地址查询参数中的授权信息。
        $paramName = $this->getAccessTokenQueryParam();
        $accessToken = $this->getRequestQueryParam($request, $paramName, $authorization);
        
        if ($accessToken === null) {
            throw new BadRequestException(strtr('Missing parameters: `{paramName}` required.', ['{paramName}' => $paramName]));
        }
        
        // 验证访问令牌。
        return $this->validateAccessToken($accessToken);
    }
    
    /**
     * {@inheritdoc}
     * 
     * @throws InvalidAccessTokenException 访问令牌无效，或者过期，或者已撤销。
     */
    public function validateAccessToken($accessToken)
    {
        $accessTokenRepository = $this->getAccessTokenRepository();
        $accessToken = $accessTokenRepository->unserializeAccessTokenEntity($accessToken, $this->getAccessTokenCryptKey());
        if (!$accessToken instanceof AccessTokenEntityInterface) {
            throw new InvalidAccessTokenException('Access token is invalid.');
        } elseif ($accessToken->getExpires() < time()) {
            throw new InvalidAccessTokenException('Access token has expired.');
        } elseif ($accessTokenRepository->isAccessTokenEntityRevoked($accessToken->getIdentifier())) {
            throw new InvalidAccessTokenException('Access token has been revoked.');
        }
        
        return $accessToken;
    }
}