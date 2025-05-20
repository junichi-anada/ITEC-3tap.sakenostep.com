<?php

namespace App\View\Composers;

use Illuminate\View\View;
// use Illuminate\Support\Facades\Auth; // ヘルパー関数を使用するため不要に

class HeaderComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $userName = getCustomerName(); // ヘルパー関数を呼び出し

        $view->with('userName', $userName);
    }
}
