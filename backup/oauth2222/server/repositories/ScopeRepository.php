<?php
/**
 * @link https://github.com/devzyj/yii2-oauth2-server
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */
namespace common\oauth2\server\repositories;

use common\oauth2\server\interfaces\ScopeRepositoryInterface;
use common\oauth2\server\entities\ScopeEntity;
use common\oauth2\server\repositories\traits\ScopeRepositoryTrait;

/**
 * ScopeRepository class.
 * 
 * @author ZhangYanJiong <zhangyanjiong@163.com>
 * @since 1.0
 */
class ScopeRepository implements ScopeRepositoryInterface
{
    use ScopeRepositoryTrait;
    
    /**
     * {@inheritdoc}
     */
    public function getScopeEntity($identifier)
    {
        return ScopeEntity::findOneByIdentifier($identifier);
    }
}