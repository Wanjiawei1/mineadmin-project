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

namespace App\Schema;

use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use JsonSerializable;

#[Schema(title: 'ProductSchema')]
class ProductSchema implements JsonSerializable
{
    #[Property(property: 'id', title: '商品ID', type: 'integer')]
    public int $id;

    #[Property(property: 'name', title: '商品名称', type: 'string')]
    public string $name;

    #[Property(property: 'sn', title: '商品编号', type: 'string')]
    public string $sn;

    #[Property(property: 'description', title: '商品描述', type: 'string')]
    public ?string $description;

    #[Property(property: 'content', title: '商品详情', type: 'string')]
    public ?string $content;

    #[Property(property: 'image', title: '商品主图', type: 'string')]
    public ?string $image;

    #[Property(property: 'images', title: '商品图片集', type: 'array')]
    public ?array $images;

    #[Property(property: 'category_id', title: '分类ID', type: 'integer')]
    public ?int $category_id;

    #[Property(property: 'price', title: '商品价格', type: 'number', format: 'decimal')]
    public float $price;

    #[Property(property: 'original_price', title: '原价', type: 'number', format: 'decimal')]
    public ?float $original_price;

    #[Property(property: 'stock', title: '库存数量', type: 'integer')]
    public int $stock;

    #[Property(property: 'sales', title: '销量', type: 'integer')]
    public int $sales;

    #[Property(property: 'status', title: '状态', type: 'integer', enum: [1, 2, 3])]
    public int $status;

    #[Property(property: 'status_label', title: '状态标签', type: 'string')]
    public string $status_label;

    #[Property(property: 'sort', title: '排序', type: 'integer')]
    public int $sort;

    #[Property(property: 'weight', title: '重量(kg)', type: 'number', format: 'decimal')]
    public ?float $weight;

    #[Property(property: 'unit', title: '单位', type: 'string')]
    public string $unit;

    #[Property(property: 'specs', title: '商品规格', type: 'array')]
    public ?array $specs;

    #[Property(property: 'attributes', title: '商品属性', type: 'array')]
    public ?array $attributes;

    #[Property(property: 'is_virtual', title: '是否虚拟商品', type: 'integer', enum: [0, 1])]
    public int $is_virtual;

    #[Property(property: 'is_hot', title: '是否热门', type: 'integer', enum: [0, 1])]
    public int $is_hot;

    #[Property(property: 'is_recommend', title: '是否推荐', type: 'integer', enum: [0, 1])]
    public int $is_recommend;

    #[Property(property: 'shelf_time', title: '上架时间', type: 'string', format: 'date-time')]
    public ?string $shelf_time;

    #[Property(property: 'created_by', title: '创建者', type: 'integer')]
    public int $created_by;

    #[Property(property: 'updated_by', title: '更新者', type: 'integer')]
    public ?int $updated_by;

    #[Property(property: 'created_at', title: '创建时间', type: 'string', format: 'date-time')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'string', format: 'date-time')]
    public string $updated_at;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sn' => $this->sn,
            'description' => $this->description,
            'content' => $this->content,
            'image' => $this->image,
            'images' => $this->images,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'original_price' => $this->original_price,
            'stock' => $this->stock,
            'sales' => $this->sales,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'sort' => $this->sort,
            'weight' => $this->weight,
            'unit' => $this->unit,
            'specs' => $this->specs,
            'attributes' => $this->attributes,
            'is_virtual' => $this->is_virtual,
            'is_hot' => $this->is_hot,
            'is_recommend' => $this->is_recommend,
            'shelf_time' => $this->shelf_time,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'remark' => $this->remark,
        ];
    }
}

