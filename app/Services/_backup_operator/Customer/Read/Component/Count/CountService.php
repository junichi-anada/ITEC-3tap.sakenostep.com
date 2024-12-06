<?php

declare(strict_types=1);

namespace App\Services\Operator\Customer\Read\Component\Count;

use App\Models\Authenticate;
use App\Models\AuthenticateOauth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\Operator\Customer\Read\Component\Count\UserCountService;
use App\Services\Operator\Customer\Read\Component\Count\NewUserCountService;
use App\Services\Operator\Customer\Read\Component\Count\LineUserCountService;

/**
 * 顧客数カウントサービスクラス
 *
 * このクラスは顧客数をカウントするためのサービスを提供します。
 */
final class CountService
{
    private UserCountService $userCountService;
    private NewUserCountService $newUserCountService;
    private LineUserCountService $lineUserCountService;

    public function __construct(
        UserCountService $userCountService,
        NewUserCountService $newUserCountService,
        LineUserCountService $lineUserCountService
    ) {
        $this->userCountService = $userCountService;
        $this->newUserCountService = $newUserCountService;
        $this->lineUserCountService = $lineUserCountService;
    }

    /**
     * 各種カウントを取得
     *
     * @return array{
     *  userCount: int,
     *  newUserCount: int,
     *  lineUserCount: int
     * }
     */
    public function getCounts(): array
    {
        return [
            'userCount' => $this->userCountService->getUserCount(),
            'newUserCount' => $this->newUserCountService->getNewUserCount(),
            'lineUserCount' => $this->lineUserCountService->getLineUserCount(),
        ];
    }

    public function getUserCount(): int
    {
        return $this->userCountService->getUserCount();
    }

    public function getNewUserCount(): int
    {
        return $this->newUserCountService->getNewUserCount();
    }

    public function getLineUserCount(): int
    {
        return $this->lineUserCountService->getLineUserCount();
    }
}
