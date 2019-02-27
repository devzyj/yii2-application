<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2019 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

Yii::setAlias('@application', dirname(dirname(__DIR__)));

Yii::setAlias('@common', '@application/common');

Yii::setAlias('@console', '@application/console');

Yii::setAlias('@backend', '@application/backend');
Yii::setAlias('@backendApi', '@application/backend-api');

Yii::setAlias('@frontend', '@application/frontend');



Yii::setAlias('@devjerry/yii2/adminlte', '@application/devjerry/yii2-adminlte');