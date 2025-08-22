<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Http\Admin\Controller;

use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\ProductRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Model\Enums\Product\Status;
use App\Schema\ProductSchema;
use App\Service\ProductService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Mine\Swagger\Attributes\ResultResponse;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class ProductController extends AbstractController
{
    public function __construct(
        protected readonly ProductService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/product/list',
        operationId: 'ProductList',
        summary: '商品列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:list')]
    #[PageResponse(instance: ProductSchema::class)]
    public function list(): Result
    {
        $params = $this->getRequest()->all();
        $data = $this->service->page($params, $this->getCurrentPage(), $this->getPageSize());
        
        // 添加状态标签
        $data->getCollection()->transform(function ($item) {
            $item->status_label = $item->status->label();
            return $item;
        });
        
        return $this->success($data);
    }

    #[Post(
        path: '/admin/product',
        operationId: 'CreateProduct',
        summary: '创建商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:create')]
    #[ResultResponse(instance: new Result())]
    public function create(ProductRequest $request): Result
    {
        $data = $request->validated();
        $data['created_by'] = $this->currentUser->id();
        
        try {
            $product = $this->service->create($data);
            return $this->success($product, '商品创建成功');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Get(
        path: '/admin/product/{id}',
        operationId: 'ShowProduct',
        summary: '商品详情',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:view')]
    #[ResultResponse(instance: new Result())]
    public function show(int $id): Result
    {
        $product = $this->service->getRepository()->findById($id);
        if (!$product) {
            return $this->error('商品不存在');
        }
        
        $product->status_label = $product->status->label();
        return $this->success($product);
    }

    #[Put(
        path: '/admin/product/{id}',
        operationId: 'UpdateProduct',
        summary: '更新商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:update')]
    #[ResultResponse(instance: new Result())]
    public function update(int $id, ProductRequest $request): Result
    {
        $data = $request->validated();
        $data['updated_by'] = $this->currentUser->id();
        
        try {
            $product = $this->service->update($id, $data);
            return $this->success($product, '商品更新成功');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Delete(
        path: '/admin/product/{id}',
        operationId: 'DeleteProduct',
        summary: '删除商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:delete')]
    #[ResultResponse(instance: new Result())]
    public function delete(int $id): Result
    {
        try {
            $this->service->delete($id);
            return $this->success(null, '商品删除成功');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Post(
        path: '/admin/product/batch/delete',
        operationId: 'BatchDeleteProduct',
        summary: '批量删除商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:delete')]
    #[ResultResponse(instance: new Result())]
    public function batchDelete(): Result
    {
        $ids = $this->getRequest()->input('ids', []);
        if (empty($ids)) {
            return $this->error('请选择要删除的商品');
        }
        
        try {
            $count = $this->service->batchDelete($ids);
            return $this->success(['count' => $count], "成功删除 {$count} 个商品");
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Post(
        path: '/admin/product/{id}/on-shelf',
        operationId: 'OnShelfProduct',
        summary: '上架商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:shelf')]
    #[ResultResponse(instance: new Result())]
    public function onShelf(int $id): Result
    {
        try {
            $this->service->onShelf($id);
            return $this->success(null, '商品上架成功');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Post(
        path: '/admin/product/{id}/off-shelf',
        operationId: 'OffShelfProduct',
        summary: '下架商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:shelf')]
    #[ResultResponse(instance: new Result())]
    public function offShelf(int $id): Result
    {
        try {
            $this->service->offShelf($id);
            return $this->success(null, '商品下架成功');
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Post(
        path: '/admin/product/batch/on-shelf',
        operationId: 'BatchOnShelfProduct',
        summary: '批量上架商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:shelf')]
    #[ResultResponse(instance: new Result())]
    public function batchOnShelf(): Result
    {
        $ids = $this->getRequest()->input('ids', []);
        if (empty($ids)) {
            return $this->error('请选择要上架的商品');
        }
        
        try {
            $count = $this->service->batchOnShelf($ids);
            return $this->success(['count' => $count], "成功上架 {$count} 个商品");
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Post(
        path: '/admin/product/batch/off-shelf',
        operationId: 'BatchOffShelfProduct',
        summary: '批量下架商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:shelf')]
    #[ResultResponse(instance: new Result())]
    public function batchOffShelf(): Result
    {
        $ids = $this->getRequest()->input('ids', []);
        if (empty($ids)) {
            return $this->error('请选择要下架的商品');
        }
        
        $count = $this->service->batchOffShelf($ids);
        return $this->success(['count' => $count], "成功下架 {$count} 个商品");
    }

    #[Post(
        path: '/admin/product/{id}/adjust-stock',
        operationId: 'AdjustProductStock',
        summary: '调整商品库存',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:stock')]
    #[ResultResponse(instance: new Result())]
    public function adjustStock(int $id): Result
    {
        $quantity = (int) $this->getRequest()->input('quantity', 0);
        $type = $this->getRequest()->input('type', 'increase'); // increase 或 decrease
        
        if ($quantity <= 0) {
            return $this->error('数量必须大于0');
        }
        
        if (!in_array($type, ['increase', 'decrease'])) {
            return $this->error('操作类型无效');
        }
        
        try {
            $result = $this->service->adjustStock($id, $quantity, $type);
            if ($result) {
                $action = $type === 'increase' ? '增加' : '减少';
                return $this->success(null, "库存{$action}成功");
            } else {
                return $this->error('库存调整失败');
            }
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage());
        }
    }

    #[Get(
        path: '/admin/product/stats',
        operationId: 'ProductStats',
        summary: '商品统计',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:stats')]
    #[ResultResponse(instance: new Result())]
    public function stats(): Result
    {
        $stats = $this->service->getStats();
        return $this->success($stats);
    }

    #[Get(
        path: '/admin/product/low-stock',
        operationId: 'LowStockProducts',
        summary: '库存不足商品',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[Permission(code: 'product:stock')]
    #[ResultResponse(instance: new Result())]
    public function lowStock(): Result
    {
        $threshold = (int) $this->getRequest()->input('threshold', 10);
        $products = $this->service->getLowStockProducts($threshold);
        return $this->success($products);
    }

    #[Get(
        path: '/admin/product/status-options',
        operationId: 'ProductStatusOptions',
        summary: '商品状态选项',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['商品管理'],
    )]
    #[ResultResponse(instance: new Result())]
    public function statusOptions(): Result
    {
        return $this->success(Status::options());
    }
}

