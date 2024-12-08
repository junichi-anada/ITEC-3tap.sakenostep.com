<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Customer;

use Illuminate\View\Component;

/**
 * 顧客検索フォームコンポーネント
 */
class CustomerSearchFormComponent extends Component
{
    /**
     * @param array $searchParams 検索パラメータ
     */
    public function __construct(
        public readonly array $searchParams = []
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.customer-search-form-component');
    }
}
