<?php

namespace App\View\Components\Widget\Operator\Item\Inner;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ItemInfoComponent extends Component
{
    public function __construct()
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.operator.item.inner.ItemInfo');
    }
}
