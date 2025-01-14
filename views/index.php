<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
  <title>ボウリングゲーム</title>
  <link rel="preconnect"
        href="https://fonts.googleapis.com">
  <link rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap"
        rel="stylesheet">
  <link rel="stylesheet"
        href="./css/reset.css">
  <link rel="stylesheet"
        href="./css/style.css">
</head>

<body>
  <div class="bowling-wrapper">
    <h1 class="bowling-title">ボウリングゲーム</h1>
    <table id="score-board"
           class="score-board">
      <tr>
        <?php for ($i = 1; $i <= 10; $i++): ?>
        <?php if ($i === 10): ?>
        <th colspan="3"><?php echo $i; ?></th>
        <?php else: ?>
        <th colspan="2"><?php echo $i; ?></th>
        <?php endif; ?>
        <?php endfor; ?>
      </tr>
      <tr class="throw-score">
        <?php for ($i = 1; $i <= 10; $i++): ?>
        <?php if ($i === 10): ?>
        <td></td>
        <td></td>
        <td></td>
        <?php else: ?>
        <td></td>
        <td></td>
        <?php endif; ?>
        <?php endfor; ?>
      </tr>
      <tr class="total-score">
        <?php for ($i = 1; $i <= 10; $i++): ?>
        <?php if ($i === 10): ?>
        <td colspan="3"></td>
        <?php else: ?>
        <td colspan="2"></td>
        <?php endif; ?>
        <?php endfor; ?>
      </tr>
    </table>
    <div id="bowling-visual"
         class="bowling-visual">
      <div class="bowling-ball"></div>
      <div class="bowling-result">
        <p></p>
      </div>
    </div>
    <div class="btn-group">
      <button id="throw-btn"
              class="btn throw">ボールを投げる</button>
      <button id="reset-btn"
              class="btn reset">最初から始める</button>
    </div>
  </div>
  <script src="main.js"></script>
</body>

</html>