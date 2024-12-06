<?php

namespace App\View\Components\Widget\Operator\Order;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DownloadProcessComponent extends Component
{
    public function __construct()
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.operator.order.DownloadProcess');
    }
}
