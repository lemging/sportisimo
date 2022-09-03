<?php declare(strict_types = 1);

namespace App\Core\Control\OrderControl;

interface IOrderControlFactory
{
    /**
     * @param array<int> $orders
     */
    public function create(int $order, array $orders): OrderControl;
}
