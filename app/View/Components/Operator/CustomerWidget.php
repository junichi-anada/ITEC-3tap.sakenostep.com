<?php

namespace App\View\Components\Operator;

use Illuminate\View\Component;
use App\Services\Customer\CountService;

class CustomerWidget extends Component
{
    public $userCount;
    public $newUserCount;
    public $lineUserCount;

    protected $countService;

    public function __construct(countService $countService)
    {
        $this->countService = $countService;
        $this->userCount = $this->countService->getUserCount();
        $this->newUserCount = $this->countService->getNewUserCount();
        $this->lineUserCount = $this->countService->getLineUserCount();
    }

    public function render()
    {
        return view('components.operator.user-widget');
    }
}