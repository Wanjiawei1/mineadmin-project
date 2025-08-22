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

namespace App\Service;

use App\Model\Product;
use App\Model\Enums\Product\Status;
use App\Repository\ProductRepository;

/**
 * @extends IService<Product>
 */
final class ProductService extends IService
{
    public function __construct(
        protected readonly ProductRepository $repository
    ) {}

    /**
     * 创建商品
     */
    public function create(array $data): Product
    {
        // 验证商品编号唯一性
        if (!empty($data['sn']) && $this->repository->existsBySn($data['sn'])) {
            throw new \RuntimeException('商品编号已存在');
        }

        // 设置创建者
        if (empty($data['created_by'])) {
            throw new \RuntimeException('创建者不能为空');
        }

        // 设置默认值
        $data = array_merge([
            'status' => Status::OFF_SHELF->value,
            'stock' => 0,
            'sales' => 0,
            'sort' => 0,
            'is_virtual' => 0,
            'is_hot' => 0,
            'is_recommend' => 0,
            'unit' => '件',
        ], $data);

        return $this->repository->create($data);
    }

    /**
     * 更新商品
     */
    public function update(int $id, array $data): Product
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new \RuntimeException('商品不存在');
        }

        // 验证商品编号唯一性
        if (!empty($data['sn']) && $this->repository->existsBySn($data['sn'], $id)) {
            throw new \RuntimeException('商品编号已存在');
        }

        return $this->repository->update($id, $data);
    }

    /**
     * 删除商品
     */
    public function delete(int $id): bool
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new \RuntimeException('商品不存在');
        }

        // 如果商品已上架，不允许删除
        if ($product->status === Status::ON_SHELF) {
            throw new \RuntimeException('上架商品不能删除，请先下架');
        }

        return $this->repository->deleteById($id);
    }

    /**
     * 批量删除商品
     */
    public function batchDelete(array $ids): int
    {
        // 检查是否有上架商品
        $onShelfCount = Product::whereIn('id', $ids)
            ->where('status', Status::ON_SHELF)
            ->count();
        
        if ($onShelfCount > 0) {
            throw new \RuntimeException('存在上架商品，不能删除');
        }

        return $this->repository->deleteByIds($ids);
    }

    /**
     * 上架商品
     */
    public function onShelf(int $id): bool
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new \RuntimeException('商品不存在');
        }

        // 检查必要信息
        if (empty($product->name) || empty($product->price)) {
            throw new \RuntimeException('商品信息不完整，无法上架');
        }

        return $product->onShelf();
    }

    /**
     * 下架商品
     */
    public function offShelf(int $id): bool
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new \RuntimeException('商品不存在');
        }

        return $product->offShelf();
    }

    /**
     * 批量上架
     */
    public function batchOnShelf(array $ids): int
    {
        // 验证商品信息完整性
        $invalidProducts = Product::whereIn('id', $ids)
            ->where(function ($query) {
                $query->whereNull('name')
                      ->orWhereNull('price')
                      ->orWhere('name', '')
                      ->orWhere('price', 0);
            })
            ->count();

        if ($invalidProducts > 0) {
            throw new \RuntimeException('存在信息不完整的商品，无法批量上架');
        }

        return $this->repository->batchOnShelf($ids);
    }

    /**
     * 批量下架
     */
    public function batchOffShelf(array $ids): int
    {
        return $this->repository->batchOffShelf($ids);
    }

    /**
     * 调整库存
     */
    public function adjustStock(int $id, int $quantity, string $type = 'increase'): bool
    {
        $product = $this->repository->findById($id);
        if (!$product) {
            throw new \RuntimeException('商品不存在');
        }

        if ($type === 'increase') {
            return $product->increaseStock($quantity);
        } else {
            return $product->decreaseStock($quantity);
        }
    }

    /**
     * 获取商品统计信息
     */
    public function getStats(): array
    {
        return $this->repository->getSalesStats();
    }

    /**
     * 获取库存不足商品
     */
    public function getLowStockProducts(int $threshold = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getLowStockProducts($threshold);
    }

    /**
     * 获取热门商品
     */
    public function getHotProducts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('is_hot', 1)
            ->where('status', Status::ON_SHELF)
            ->orderBy('sales', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 获取推荐商品
     */
    public function getRecommendProducts(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Product::where('is_recommend', 1)
            ->where('status', Status::ON_SHELF)
            ->orderBy('sort', 'desc')
            ->orderBy('sales', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * 搜索商品
     */
    public function search(string $keyword, array $params = []): \Hyperf\Paginator\LengthAwarePaginator
    {
        $params['keyword'] = $keyword;
        return $this->repository->getOnShelfProducts($params);
    }

    public function getRepository(): ProductRepository
    {
        return $this->repository;
    }
}

