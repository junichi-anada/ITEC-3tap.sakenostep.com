<?php

namespace App\View\Components\Widget\Operator\Order\Inner;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PersonalInfoComponent extends Component
{
    public function __construct()
    {
    }

    public function render(): View|Closure|string
    {
        return view('components.widget.operator.order.inner.PersonalInfo');
    }
}
