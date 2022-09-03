<?php

namespace App\Core\Control\PaginatorControl;

use App\Common\Core\Control\AbstractControl;
use App\Common\Core\Control\TRenderable;
use Nette\Application\UI\Template;
use Nette\Utils\Paginator;

final class PaginatorControl extends AbstractControl
{
    use TRenderable;

    public const ONE_PAGE_RESULT_LIMITS = [3, 5, 10, 25, 50, 100];

    /** @var array<callable> */
    public array $onPageChange = [];

    /** @var array<callable> */
    public array $onLimitChange = [];

	protected int $limit = 10;

    /** @var array<int> */
    public array $limits = self::ONE_PAGE_RESULT_LIMITS;

	protected Paginator $paginator;

	public function __construct(Paginator $paginator)
	{
		$this->paginator = $paginator;
	}

    public function setDefaultVariables(Template $template): void
    {
        parent::setDefaultVariables($template);

        $template->paginator = $this->paginator;
        $template->limits = $this->limits;
    }

	public function handlePage(int $page)
	{
	    $this->onPageChange(intval($page));
	}

	public function handleLimit(int $limit)
    {
        $this->onLimitChange($limit);
    }
}
