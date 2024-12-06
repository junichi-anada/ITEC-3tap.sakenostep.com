<?php

namespace App\Services\ItemCategory\Actions;

use App\Models\ItemCategory;
use App\Services\ItemCategory\DTOs\CategoryData;
use App\Services\ItemCategory\Exceptions\CategoryException;
use App\Services\ServiceErrorHandler;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Support\Facades\Log;

class UpdateCategoryAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository
    ) {}

    /**
     * カテゴリを更新する
     *
     * @param int $id
     * @param CategoryData $data
     * @return ItemCategory
     * @throws CategoryException
     */
    public function execute(int $id, CategoryData $data): ItemCategory
    {
        return $this->tryCatchWrapper(
            function () use ($id, $data) {
                Log::info("Updating category with ID: $id");

                $category = $this->repository->findById($id);
                if (!$category) {
                    throw CategoryException::notFound($id);
                }

                $updatedCategory = $this->repository->update($id, $data->toArray());
                if (!$updatedCategory) {
                    throw CategoryException::updateFailed($id);
                }

                return $updatedCategory;
            },
            'カテゴリの更新に失敗しました',
            ['id' => $id] + $data->toArray()
        );
    }
}
