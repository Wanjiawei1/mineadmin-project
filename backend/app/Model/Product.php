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

namespace App\Model;

use App\Model\Enums\Product\Status;
use Carbon\Carbon;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 商品ID
 * @property string $name 商品名称
 * @property string $sn 商品编号
 * @property string $description 商品描述
 * @property string $content 商品详情
 * @property string $image 商品主图
 * @property array $images 商品图片集
 * @property int $category_id 分类ID
 * @property float $price 商品价格
 * @property float $original_price 原价
 * @property int $stock 库存数量
 * @property int $sales 销量
 * @property Status $status 状态
 * @property int $sort 排序
 * @property float $weight 重量
 * @property string $unit 单位
 * @property array $specs 商品规格
 * @property array $attributes 商品属性
 * @property int $is_virtual 是否虚拟商品
 * @property int $is_hot 是否热门
 * @property int $is_recommend 是否推荐
 * @property Carbon $shelf_time 上架时间
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $remark 备注
 */
final class Product extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'product';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'name', 'sn', 'description', 'content', 'image', 'images',
        'category_id', 'price', 'original_price', 'stock', 'sales',
        'status', 'sort', 'weight', 'unit', 'specs', 'attributes',
        'is_virtual', 'is_hot', 'is_recommend', 'shelf_time',
        'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'stock' => 'integer',
        'sales' => 'integer',
        'status' => Status::class,
        'sort' => 'integer',
        'weight' => 'decimal:2',
        'is_virtual' => 'integer',
        'is_hot' => 'integer',
        'is_recommend' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shelf_time' => 'datetime',
        'images' => 'json',
        'specs' => 'json',
        'attributes' => 'json',
    ];

    public function creating(Creating $event): void
    {
        // 自动生成商品编号
        if (empty($this->sn)) {
            $this->sn = $this->generateSn();
        }
    }

    /**
     * 生成商品编号
     */
    private function generateSn(): string
    {
        $prefix = 'SP';
        $date = date('Ymd');
        
        // 获取当天最大编号
        $lastProduct = static::where('sn', 'like', $prefix . $date . '%')
            ->orderBy('sn', 'desc')
            ->first();
        
        if ($lastProduct) {
            $lastNumber = (int) substr($lastProduct->sn, -4);
            $number = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $number = '0001';
        }
        
        return $prefix . $date . $number;
    }

    /**
     * 上架商品
     */
    public function onShelf(): bool
    {
        $this->status = Status::ON_SHELF;
        $this->shelf_time = Carbon::now();
        return $this->save();
    }

    /**
     * 下架商品
     */
    public function offShelf(): bool
    {
        $this->status = Status::OFF_SHELF;
        return $this->save();
    }

    /**
     * 设为售罄
     */
    public function soldOut(): bool
    {
        $this->status = Status::SOLD_OUT;
        return $this->save();
    }

    /**
     * 是否上架
     */
    public function isOnShelf(): bool
    {
        return $this->status === Status::ON_SHELF;
    }

    /**
     * 是否有库存
     */
    public function hasStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * 减少库存
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }
        
        $this->stock -= $quantity;
        $this->sales += $quantity;
        
        // 如果库存为0，设置为售罄
        if ($this->stock === 0) {
            $this->status = Status::SOLD_OUT;
        }
        
        return $this->save();
    }

    /**
     * 增加库存
     */
    public function increaseStock(int $quantity): bool
    {
        $this->stock += $quantity;
        
        // 如果原来是售罄状态，恢复为上架
        if ($this->status === Status::SOLD_OUT && $this->stock > 0) {
            $this->status = Status::ON_SHELF;
        }
        
        return $this->save();
    }
}

