<?php
/**
 * @link https://github.com/devzyj/yii2-application
 * @copyright Copyright (c) 2018 Zhang Yan Jiong
 * @license http://opensource.org/licenses/BSD-3-Clause
 */

return [
    // 超级客户端（IDs）。
    'superClients' => [],
    // 允行批量操作的资源个数。
    'rest.batch.allowedCount' => 100,
    // 查询数据时，默认每页显示条数。
    'rest.search.pagination.defaultPageSize' => 20,
    // 查询数据时，每页显示条数的限制。
    'rest.search.pagination.pageSizeLimit' => [1, 100],
    // 查询数据时，排序是否可以作用于多个属性。
    'rest.search.sort.enableMultiSort' => true,
];
