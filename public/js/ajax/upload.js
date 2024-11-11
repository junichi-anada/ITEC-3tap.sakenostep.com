document.addEventListener("DOMContentLoaded", function () {
    const uploadResultsContainer = document.getElementById("upload-results");

    function processUpload(data) {
        // データ処理のロジックをここに記述
        // 例: データを1件ずつ処理する
        data.forEach((item, index) => {
            // 処理が完了したら結果を表示
            setTimeout(() => {
                const resultDiv = document.createElement("div");
                resultDiv.className =
                    "flex hover:bg-[#F4CF41] hover:bg-opacity-40 cursor-pointer";
                resultDiv.innerHTML = `
                    <div class="text-center p-4 flex-1 text-sm">${item.code}</div>
                    <div class="text-center p-4 flex-1 text-sm">${item.name}</div>
                    <div class="text-center p-4 flex-1 text-sm">${item.phone}</div>
                    <div class="text-center p-4 w-1/4 text-sm">${item.address}</div>
                    <div class="text-center p-4 flex-1 text-sm">${item.result}</div>
                `;
                uploadResultsContainer.appendChild(resultDiv);
            }, index * 1000); // 1秒ごとに表示
        });
    }

    // バックエンドからデータを取得
    fetch("/api/upload-data") // 適切なAPIエンドポイントに変更
        .then((response) => {
            if (!response.ok) {
                throw new Error("ネットワークエラー");
            }
            return response.json();
        })
        .then((data) => {
            processUpload(data); // 取得したデータをprocessUploadに渡す
        })
        .catch((error) => {
            console.error("エラー:", error);
            // エラーハンドリングの処理をここに追加
        });

    // 例: アップロード処理が完了した後に呼び出す
    // const sampleData = [
    //     {
    //         code: "11",
    //         name: "111",
    //         phone: "11111",
    //         address: "1111111",
    //         result: "成功",
    //     },
    //     {
    //         code: "12",
    //         name: "222",
    //         phone: "22222",
    //         address: "2222222",
    //         result: "成功",
    //     },
    //     {
    //         code: "12",
    //         name: "222",
    //         phone: "22222",
    //         address: "2222222",
    //         result: "成功",
    //     },
    // ];
    // processUpload(sampleData);
});
