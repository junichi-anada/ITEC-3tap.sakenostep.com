<?php

namespace App\View\Components\Operator\Widgets\Customer;

use Illuminate\View\Component;
use App\Models\LineUser;

class LineMessageFormComponent extends Component
{
    /**
     * LINE連携情報
     *
     * @var LineUser|null
     */
    public $lineUser;

    /**
     * Create a new component instance.
     *
     * @param LineUser|null $lineUser
     * @return void
     */
    public function __construct(?LineUser $lineUser)
    {
        $this->lineUser = $lineUser;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.customer.line-message-form-component');
    }
}
