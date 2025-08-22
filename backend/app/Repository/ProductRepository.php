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

namespace App\Repository;

use App\Model\Product;
use App\Model\Enums\Product\Status;
use App\Repository\Traits\RepositoryTrait;

/**
 * @implements IRepository<Product>
 */
final class ProductRepository implements IRepository
{
    use RepositoryTrait;

    public function __construct(
        protected readonly Product $model
    ) {}

    /**
     * 根据商品编号查找商品
     */
    public function findBySn(string $sn): ?Product
    {
        return $this->model->where('sn', $sn)->first();
    }

    /**
     * 检查商品编号是否存在
     */
    public function existsBySn(string $sn, ?int $excludeId = null): bool
    {
        $query = $this->model->where('sn', $sn);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * 获取上架商品列表
     */
    public function getOnShelfProducts(array $params = []): \Hyperf\Paginator\LengthAwarePaginator
    {
        $query = $this->model->where('status', Status::ON_SHELF);
        
        // 分类筛选
        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }
        
        // 关键词搜索
        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['keyword'] . '%')
                  ->orWhere('sn', 'like', '%' . $params['keyword'] . '%')
                  ->orWhere('description', 'like', '%' . $params['keyword'] . '%');
            });
        }
        
        // 价格区间
        if (!empty($params['min_price'])) {
            $query->where('price', '>=', $params['min_price']);
        }
        if (!empty($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }
        
        // 是否热门
        if (isset($params['is_hot'])) {
            $query->where('is_hot', $params['is_hot']);
        }
        
        // 是否推荐
        if (isset($params['is_recommend'])) {
            $query->where('is_recommend', $params['is_recommend']);
        }
        
        // 排序
        $orderBy = $params['order_by'] ?? 'sort';
        $orderDir = $params['order_dir'] ?? 'desc';
        $query->orderBy($orderBy, $orderDir);
        
        $page = $params['page'] ?? 1;
        $pageSize = $params['page_size'] ?? 20;
        
        return $query->paginate($pageSize, ['*'], 'page', $page);
    }

    /**
     * 批量上架
     */
    public function batchOnShelf(array $ids): int
    {
        return $this->model->whereIn('id', $ids)
            ->update([
                'status' => Status::ON_SHELF,
                'shelf_time' => now(),
                'updated_at' => now()
            ]);
    }

    /**
     * 批量下架
     */
    public function batchOffShelf(array $ids): int
    {
        return $this->model->whereIn('id', $ids)
            ->update([
                'status' => Status::OFF_SHELF,
                'updated_at' => now()
            ]);
    }

    /**
     * 获取库存不足的商品
     */
    public function getLowStockProducts(int $threshold = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('stock', '<=', $threshold)
            ->where('status', '!=', Status::SOLD_OUT)
            ->orderBy('stock', 'asc')
            ->get();
    }

    /**
     * 根据分类获取商品数量
     */
    public function getCountByCategory(): array
    {
        return $this->model->selectRaw('category_id, count(*) as count')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->pluck('count', 'category_id')
            ->toArray();
    }

    /**
     * 获取销量统计
     */
    public function getSalesStats(): array
    {
        $total = $this->model->sum('sales');
        $onShelf = $this->model->where('status', Status::ON_SHELF)->count();
        $offShelf = $this->model->where('status', Status::OFF_SHELF)->count();
        $soldOut = $this->model->where('status', Status::SOLD_OUT)->count();
        
        return [
            'total_sales' => $total,
            'on_shelf_count' => $onShelf,
            'off_shelf_count' => $offShelf,
            'sold_out_count' => $soldOut,
            'total_count' => $onShelf + $offShelf + $soldOut,
        ];
    }

    public function page(array $params, int $currentPage, int $pageSize): \Hyperf\Paginator\LengthAwarePaginator
    {
        $query = $this->model->query();
        
        // 状态筛选
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        
        // 分类筛选
        if (!empty($params['category_id'])) {
            $query->where('category_id', $params['category_id']);
        }
        
        // 关键词搜索
        if (!empty($params['keyword'])) {
            $query->where(function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['keyword'] . '%')
                  ->orWhere('sn', 'like', '%' . $params['keyword'] . '%')
                  ->orWhere('description', 'like', '%' . $params['keyword'] . '%');
            });
        }
        
        // 创建者筛选
        if (!empty($params['created_by'])) {
            $query->where('created_by', $params['created_by']);
        }
        
        // 时间范围筛选
        if (!empty($params['start_time'])) {
            $query->where('created_at', '>=', $params['start_time']);
        }
        if (!empty($params['end_time'])) {
            $query->where('created_at', '<=', $params['end_time']);
        }
        
        // 排序
        $query->orderBy('sort', 'desc')
              ->orderBy('id', 'desc');
        
        return $query->paginate($pageSize, ['*'], 'page', $currentPage);
    }
}

