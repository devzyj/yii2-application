<?php
/**
 * @link https://github.com/devzyj/php-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace devjerry\oauth2\server\authorizes;

/**
 * CodeAuthorize class.
 *
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class CodeAuthorize extends AbstractAuthorize
{
    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return self::RESPONSE_TYPE_CODE;
    }

    /**
     * {@inheritdoc}
     */
    public function getGrantIdentifier()
    {
        return self::GRANT_TYPE_AUTHORIZATION_CODE;
    }
    
    /**
     * {@inheritdoc}
     */
    public function run(AuthorizeRequestInterface $authorizeRequest)
    {
        
    }
}