<?php

namespace App\Services\Item\Queries;

use App\Services\Item\DTOs\ItemSearchCriteria;
use App\Services\Item\Exceptions\ItemException;
use App\Services\ServiceErrorHandler;
use App\Repositories\Item\ItemRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class SearchItemsByKeywordQuery
{
    use ServiceErrorHandler;

    public function __construct(
        private ItemRepository $repository
    ) {}

    /**
     * キーワードによる商品検索を実行
     *
     * @param ItemSearchCriteria $criteria
     * @return LengthAwarePaginator
     * @throws ItemException
     */
    public function execute(ItemSearchCriteria $criteria): LengthAwarePaginator
    {
        return $this->tryCatchWrapper(
            function () use ($criteria) {
                Log::info("Searching items with keyword: {$criteria->keyword}");

                if (empty($criteria->keyword)) {
                    throw ItemException::searchFailed('検索キーワードが指定されていません。');
                }

                $searchFields = ['name', 'item_code', 'description'];
                $result = $this->repository->searchByKeyword(
                    searchFields: $searchFields,
                    keyword: $criteria->keyword,
                    conditions: $criteria->getConditions(),
                    perPage: $criteria->perPage
                );

                if ($result->isEmpty()) {
                    Log::info("No items found for keyword: {$criteria->keyword}");
                }

                return $result;
            },
            'キーワードによる商品検索に失敗しました',
            $criteria->toArray()
        );
    }
}
