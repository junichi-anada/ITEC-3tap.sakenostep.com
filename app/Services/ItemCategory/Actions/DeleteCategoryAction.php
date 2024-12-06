<?php

namespace App\Services\ItemCategory\Actions;

use App\Services\ItemCategory\Exceptions\CategoryException;
use App\Services\ServiceErrorHandler;
use App\Repositories\ItemCategory\ItemCategoryRepository;
use Illuminate\Support\Facades\Log;

class DeleteCategoryAction
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemCategoryRepository $repository
    ) {}

    /**
     * カテゴリを削除する
     *
     * @param int $id
     * @return bool
     * @throws CategoryException
     */
    public function execute(int $id): bool
    {
        return $this->tryCatchWrapper(
            function () use ($id) {
                Log::info("Deleting category with ID: $id");

                $category = $this->repository->findById($id);
                if (!$category) {
                    throw CategoryException::notFound($id);
                }

                $result = $this->repository->delete($id);
                if (!$result) {
                    throw CategoryException::deleteFailed($id);
                }

                return $result;
            },
            'カテゴリの削除に失敗しました',
            ['id' => $id]
        );
    }
}
