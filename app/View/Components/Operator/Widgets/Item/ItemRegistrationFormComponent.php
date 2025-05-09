<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\Item;

use App\Models\ItemCategory;
use App\Models\ItemUnit;
use Illuminate\View\Component;

class ItemRegistrationFormComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.item.item-registration-form-component', [
            'categories' => ItemCategory::all(),
            'units' => ItemUnit::all()
        ]);
    }
}
