<?php

namespace App\View\Components\Widget\Operator\Customer;

use Illuminate\View\Component;
use App\Services\Operator\Customer\Read\Component\Count\CountService;

class RegistEachAreaComponent extends Component
{
    public $userCount;
    public $newUserCount;
    public $lineUserCount;

    protected $countService;

    public function __construct(CountService $countService)
    {
        $this->countService = $countService;
        $this->userCount = $this->countService->getUserCount();
        $this->newUserCount = $this->countService->getNewUserCount();
        $this->lineUserCount = $this->countService->getLineUserCount();
    }

    public function render()
    {
        return view('components.widget.operator.customer.RegistEachArea');
    }
}
