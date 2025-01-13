<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ボウリングゲーム</title>
</head>

<body>
  <button id="throwBtn">ボールを投げる</button>
  <div id="scoreBoard"></div>


  <script>
    const throwBtn = document.getElementById('throwBtn');
    const scoreBoard = document.getElementById('scoreBoard');

    // 投球リクエストを送信
    throwBtn.addEventListener('click', () => {
      // PHPファイルにPOSTリクエストを送る
      fetch('throwBall.php', {method: 'POST'})
      .then(response => response.json())
      .then(res => {
        console.log(res); // やりたい処理
      })
      .catch(error => {
        console.log(error); // エラー表示
      })
    })
  </script>
</body>

</html>