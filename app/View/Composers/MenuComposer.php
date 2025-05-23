<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log; // Logファサードは不要になったので削除

class MenuComposer
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
