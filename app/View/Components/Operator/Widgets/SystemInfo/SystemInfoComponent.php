<?php

declare(strict_types=1);

namespace App\View\Components\Operator\Widgets\SystemInfo;

use App\Services\SystemInfo\SystemInfoService;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

/**
 * システム管理者からのお知らせ表示コンポーネント
 */
class SystemInfoComponent extends Component
{
    /**
     * @var Collection お知らせ一覧
     */
    public Collection $systemInfos;

    public function __construct(
        private readonly SystemInfoService $service
    ) {
        $this->systemInfos = $this->service->getSystemInfo();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.operator.widgets.system-info.system-info-component');
    }
}
