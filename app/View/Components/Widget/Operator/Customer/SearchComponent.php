<?php

namespace App\View\Components\Widget\Operator\Customer;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchComponent extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.operator.customer.Search');
    }
}
