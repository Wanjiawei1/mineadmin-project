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

namespace App\Http\Admin\Request;

use Hyperf\Validation\Request\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $method = $this->getMethod();
        $id = $this->route('id');

        $rules = [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'image' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'string|max:500',
            'category_id' => 'nullable|integer|min:1',
            'price' => 'required|numeric|min:0|max:999999.99',
            'original_price' => 'nullable|numeric|min:0|max:999999.99',
            'stock' => 'required|integer|min:0',
            'status' => 'required|integer|in:1,2,3',
            'sort' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0|max:99999.99',
            'unit' => 'nullable|string|max:20',
            'specs' => 'nullable|array',
            'attributes' => 'nullable|array',
            'is_virtual' => 'nullable|integer|in:0,1',
            'is_hot' => 'nullable|integer|in:0,1',
            'is_recommend' => 'nullable|integer|in:0,1',
            'remark' => 'nullable|string|max:500',
        ];

        // 新增时商品编号可以为空（自动生成），更新时如果提供则验证唯一性
        if ($method === 'POST') {
            $rules['sn'] = 'nullable|string|max:100|unique:product,sn';
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            $rules['sn'] = 'nullable|string|max:100|unique:product,sn,' . $id;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => '商品名称不能为空',
            'name.max' => '商品名称不能超过200个字符',
            'sn.unique' => '商品编号已存在',
            'sn.max' => '商品编号不能超过100个字符',
            'description.max' => '商品描述不能超过1000个字符',
            'image.max' => '图片路径不能超过500个字符',
            'images.array' => '商品图片集必须是数组格式',
            'images.*.string' => '图片路径必须是字符串',
            'images.*.max' => '图片路径不能超过500个字符',
            'category_id.integer' => '分类ID必须是整数',
            'category_id.min' => '分类ID必须大于0',
            'price.required' => '商品价格不能为空',
            'price.numeric' => '商品价格必须是数字',
            'price.min' => '商品价格不能小于0',
            'price.max' => '商品价格不能超过999999.99',
            'original_price.numeric' => '原价必须是数字',
            'original_price.min' => '原价不能小于0',
            'original_price.max' => '原价不能超过999999.99',
            'stock.required' => '库存数量不能为空',
            'stock.integer' => '库存数量必须是整数',
            'stock.min' => '库存数量不能小于0',
            'status.required' => '商品状态不能为空',
            'status.in' => '商品状态值无效',
            'sort.integer' => '排序必须是整数',
            'sort.min' => '排序不能小于0',
            'weight.numeric' => '重量必须是数字',
            'weight.min' => '重量不能小于0',
            'weight.max' => '重量不能超过99999.99',
            'unit.max' => '单位不能超过20个字符',
            'specs.array' => '商品规格必须是数组格式',
            'attributes.array' => '商品属性必须是数组格式',
            'is_virtual.in' => '是否虚拟商品值无效',
            'is_hot.in' => '是否热门值无效',
            'is_recommend.in' => '是否推荐值无效',
            'remark.max' => '备注不能超过500个字符',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => '商品名称',
            'sn' => '商品编号',
            'description' => '商品描述',
            'content' => '商品详情',
            'image' => '商品主图',
            'images' => '商品图片集',
            'category_id' => '分类ID',
            'price' => '商品价格',
            'original_price' => '原价',
            'stock' => '库存数量',
            'status' => '商品状态',
            'sort' => '排序',
            'weight' => '重量',
            'unit' => '单位',
            'specs' => '商品规格',
            'attributes' => '商品属性',
            'is_virtual' => '是否虚拟商品',
            'is_hot' => '是否热门',
            'is_recommend' => '是否推荐',
            'remark' => '备注',
        ];
    }
}

