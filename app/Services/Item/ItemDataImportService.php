/**
 * ファイルの重複チェック
 * @param string $fileHash
 * @param int $siteId
 * @return bool
 */
private function isDuplicateImport(string $fileHash, int $siteId): bool
{
    // 過去24時間以内の同一ハッシュのインポート履歴を確認
    $recentImport = DB::table('import_history')
        ->where('file_hash', $fileHash)
        ->where('site_id', $siteId)
        ->where('created_at', '>', now()->subHours(24))
        ->first();

    return $recentImport !== null;
}

/**
 * インポート履歴の保存
 * @param string $fileHash
 * @param int $siteId
 * @return void
 */
private function saveImportHistory(string $fileHash, int $siteId): void
{
    DB::table('import_history')->insert([
        'file_hash' => $fileHash,
        'site_id' => $siteId,
        'created_at' => now(),
    ]);
}
