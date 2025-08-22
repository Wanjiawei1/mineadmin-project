<?php

declare(strict_types=1);

use App\Model\Permission\Menu;
use App\Model\Permission\Meta;
use Hyperf\Database\Seeders\Seeder;

class MenuSeederProduct20241219 extends Seeder
{
    public const BASE_DATA = [
        'name' => '',
        'path' => '',
        'component' => '',
        'redirect' => '',
        'created_by' => 0,
        'updated_by' => 0,
        'remark' => '',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->create($this->data());
    }

    /**
     * Database seeds data.
     */
    public function data(): array
    {
        return [
            [
                'name' => 'product_center',
                'path' => '/product',
                'meta' => new Meta([
                    'title' => '商品中心',
                    'i18n' => 'product.center',
                    'icon' => 'ri:shopping-cart-2-line',
                    'type' => 'M',
                    'hidden' => 0,
                    'componentPath' => 'modules/',
                    'componentSuffix' => '.vue',
                    'breadcrumbEnable' => 1,
                    'copyright' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ]),
                'children' => [
                    [
                        'name' => 'product:manage',
                        'path' => '/product/manage',
                        'component' => 'product/views/manage/index',
                        'meta' => new Meta([
                            'type' => 'M',
                            'title' => '商品管理',
                            'i18n' => 'product.manage',
                            'icon' => 'ri:list-check-2',
                            'hidden' => 0,
                            'componentPath' => 'modules/',
                            'componentSuffix' => '.vue',
                            'breadcrumbEnable' => 1,
                            'copyright' => 1,
                            'cache' => 1,
                            'affix' => 0,
                        ]),
                        'children' => [
                            [
                                'name' => 'product:list',
                                'meta' => new Meta([
                                    'title' => '商品列表',
                                    'type' => 'B',
                                    'i18n' => 'product.list',
                                ]),
                            ],
                            [
                                'name' => 'product:create',
                                'meta' => new Meta([
                                    'title' => '创建商品',
                                    'type' => 'B',
                                    'i18n' => 'product.create',
                                ]),
                            ],
                            [
                                'name' => 'product:update',
                                'meta' => new Meta([
                                    'title' => '更新商品',
                                    'type' => 'B',
                                    'i18n' => 'product.update',
                                ]),
                            ],
                            [
                                'name' => 'product:delete',
                                'meta' => new Meta([
                                    'title' => '删除商品',
                                    'type' => 'B',
                                    'i18n' => 'product.delete',
                                ]),
                            ],
                            [
                                'name' => 'product:shelf',
                                'meta' => new Meta([
                                    'title' => '上下架',
                                    'type' => 'B',
                                    'i18n' => 'product.shelf',
                                ]),
                            ],
                            [
                                'name' => 'product:stock',
                                'meta' => new Meta([
                                    'title' => '调整库存',
                                    'type' => 'B',
                                    'i18n' => 'product.stock',
                                ]),
                            ],
                             [
                                'name' => 'product:stats',
                                'meta' => new Meta([
                                    'title' => '商品统计',
                                    'type' => 'B',
                                    'i18n' => 'product.stats',
                                ]),
                            ],
                            [
                                'name' => 'product:view',
                                'meta' => new Meta([
                                    'title' => '商品详情',
                                    'type' => 'B',
                                    'i18n' => 'product.view',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function create(array $data, int $parent_id = 0): void
    {
        foreach ($data as $v) {
            $_v = $v;
            if (isset($v['children'])) {
                unset($_v['children']);
            }
            $_v['parent_id'] = $parent_id;
            $menu = Menu::create(array_merge(self::BASE_DATA, $_v));
            if (isset($v['children']) && count($v['children'])) {
                $this->create($v['children'], $menu->id);
            }
        }
    }
}
