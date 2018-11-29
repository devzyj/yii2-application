<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    // 超级客户端 ID 列表。
    // 超级客户端不验证 `客户端状态`、`访问 IP`、`接口权限`。
    // 一般是在迁移后 IP 变更，或误改了客户端数据后不能访问接口的情况。
    'superClients' => [],
    // 速率限制。第一个元素是允许请求的最大数量，第二个元素是限制的时间（秒）。
    'rateLimit' => [99999, 1],
    // 允行批量操作的资源个数。
    'rest.batch.allowedCount' => 100,
    // 查询数据时，默认每页显示条数。
    'rest.search.pagination.defaultPageSize' => 20,
    // 查询数据时，每页显示条数的限制。
    'rest.search.pagination.pageSizeLimit' => [1, 100],
    // 查询数据时，排序是否可以作用于多个属性。
    'rest.search.sort.enableMultiSort' => true,
];
