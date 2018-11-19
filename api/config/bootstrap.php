<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

Yii::setAlias('@apiAuthorize', '@api/modules/authorize');

Yii::setAlias('@apiCgiBin', '@api/modules/cgi-bin');
Yii::setAlias('@apiCgiBinV1', '@apiCgiBin/modules/v1');

Yii::setAlias('@apiRbac', '@api/modules/rbac');
Yii::setAlias('@apiRbacV1', '@apiRbac/modules/v1');