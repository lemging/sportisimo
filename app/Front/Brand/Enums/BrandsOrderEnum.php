<?php declare(strict_types = 1);

namespace App\Front\Brand\Enums;

class BrandsOrderEnum {
    const SORT_BY_ID = 0;
    const SORT_BY_TITLE = 1;
    const SORT_BY_TITLE_UP = 2;

    /**
     * @return array<int>
     */
    static public function getOrders(): array
    {
        return [self::SORT_BY_ID, self::SORT_BY_TITLE, self::SORT_BY_TITLE_UP];
    }

    /**
     * @return array<string>
     */
    static public function getOrderDescriptions(): array
    {
        return ["Podle ID vzestupně", "Podle názvu vzestupně", "Podle názvu sestupně"];
    }
}