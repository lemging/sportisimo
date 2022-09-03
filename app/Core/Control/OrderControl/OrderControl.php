<?php declare(strict_types = 1);

namespace App\Core\Control\OrderControl;

use App\Common\Core\Control\AbstractControl;
use App\Common\Core\Control\TRenderable;
use App\Front\Brand\Enums\BrandsOrderEnum;
use Nette\Application\UI\Template;

class OrderControl extends AbstractControl
{
    use TRenderable;

    /** @var array<callable> */
    public array $onChangeOrder = [];

    private int $order;

    /** @var array<int> */
    private array $orders;

    /** @var string */
    private string $enumClass;

    /**
     * @param array<int> $orders
     */
    public function __construct(int $order, array $orders)
    {
        $this->order = $order;
        $this->orders = $orders;
    }

    protected function setDefaultVariables(Template $template): void
    {
        $template->orders = $this->orders;
        $template->activeOrder = $this->order;
        $template->orderDescriptions = BrandsOrderEnum::getOrderDescriptions();
    }

    public function handleChangeOrder(int $order): void
    {
        $this->onChangeOrder($order);
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }
}
