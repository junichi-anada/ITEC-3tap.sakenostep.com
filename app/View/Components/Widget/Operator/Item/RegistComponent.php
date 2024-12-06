<?php

namespace App\View\Components\Widget\Operator\Item;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RegistComponent extends Component
{
    public function __construct()
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.operator.item.Regist');
    }
}
