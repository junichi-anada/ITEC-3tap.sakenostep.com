<?php

namespace App\Services\ItemCategory\Actions;

use App\Models\ItemCategory;
use App\Services\ItemCategory\DTOs\CategoryData;
use App\Services\ItemCategory\Exceptions\CategoryException;
use App\Services\ServiceErrorHandler;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Support\Facades\Log;

class CreateCategoryAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository
    ) {}

    /**
     * カテゴリを作成する
     *
     * @param CategoryData $data
     * @return ItemCategory
     * @throws CategoryException
     */
    public function execute(CategoryData $data): ItemCategory
    {
        return $this->tryCatchWrapper(
            function () use ($data) {
                Log::info("Creating new category with data: " . json_encode($data->toArray()));
                
                $category = $this->repository->create($data->toArray());
                
                if (!$category) {
                    throw CategoryException::createFailed($data->toArray());
                }
                
                return $category;
            },
            'カテゴリの作成に失敗しました',
            $data->toArray()
        );
    }
}
