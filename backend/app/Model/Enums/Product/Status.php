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

namespace App\Model\Enums\Product;

/**
 * 商品状态枚举
 */
enum Status: int
{
    case OFF_SHELF = 1; // 下架
    case ON_SHELF = 2;  // 上架
    case SOLD_OUT = 3;  // 售罄

    public function label(): string
    {
        return match ($this) {
            self::OFF_SHELF => '下架',
            self::ON_SHELF => '上架',
            self::SOLD_OUT => '售罄',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OFF_SHELF => 'default',
            self::ON_SHELF => 'success',
            self::SOLD_OUT => 'warning',
        };
    }

    /**
     * 获取所有状态选项
     */
    public static function options(): array
    {
        return [
            ['value' => self::OFF_SHELF->value, 'label' => self::OFF_SHELF->label(), 'color' => self::OFF_SHELF->color()],
            ['value' => self::ON_SHELF->value, 'label' => self::ON_SHELF->label(), 'color' => self::ON_SHELF->color()],
            ['value' => self::SOLD_OUT->value, 'label' => self::SOLD_OUT->label(), 'color' => self::SOLD_OUT->color()],
        ];
    }
}

