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


  <script>
    window.addEventListener("load", () => {
      const throwBtn = document.getElementById('throw-btn');
      const resetBtn = document.getElementById('reset-btn');
      const throwScore = document.querySelector('#score-board .throw-score');
      const totalScore = document.querySelector('#score-board .total-score');
      const bowlingVisual = document.getElementById('bowling-visual');
      const bowlingResultText = document.querySelector('.bowling-result p');

      // 最初にスコアボードを表示
      fetch('getGameData.php', {
          method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
          // スコアボードを更新
          updateScoreBoard(data.frames);

          // ゲーム終了判定
          if (data.isGameOver) {
            throwBtn.textContent = 'ゲーム終了';
            throwBtn.classList.add('game-over');
            throwBtn.disabled = true; // ボタンを無効化
          }
        })
        .catch(error => {
          console.log('エラーが発生しました:', error);
        });

      bowlingVisual.addEventListener('click', () => {
        // アニメーション
        if (bowlingVisual.classList.contains('is-animation')) {
          bowlingVisual.classList.remove('is-animation');
        }
      })

      throwBtn.addEventListener('click', () => {
        // ビジュアル画面までスクロール
        const bowlingVisualDOMRect = bowlingVisual.getBoundingClientRect();
        const bowlingVisualTop = bowlingVisualDOMRect.top + window.pageYOffset;
        window.scrollTo({
          top: bowlingVisualTop,
          behavior: 'smooth'
        });

        // アニメーション
        if (bowlingVisual.classList.contains('is-animation')) {
          bowlingVisual.classList.remove('is-animation');
          setTimeout(() => {
            bowlingVisual.classList.add('is-animation');
          }, 200)
        } else {
          bowlingVisual.classList.add('is-animation');
        }


        // 投球リクエストを送信
        // PHPファイルにPOSTリクエストを送る
        fetch('throwBall.php', {
            method: 'POST'
          })
          .then(response => response.json())
          .then(data => {
            // ビジュアルアニメーションに投球結果を表示
            resultScoreVisual(data.frames);
            // スコアボードを更新
            setTimeout(() => {
              updateScoreBoard(data.frames);
            }, 1300)

            // ゲーム終了判定
            if (data.isGameOver) {
              throwBtn.textContent = 'ゲーム終了';
              throwBtn.classList.add('game-over');
              throwBtn.disabled = true; // ボタンを無効化
            }
          })
          .catch(error => {
            console.log('エラーが発生しました:', error);
          });
      });

      // ゲームリセットリクエストを送信
      resetBtn.addEventListener('click', () => {
        // アニメーション
        bowlingVisual.classList.remove('is-animation');

        // PHPファイルにPOSTリクエストを送る
        fetch('resetGame.php', {
            method: 'POST'
          })
          .then(response => response.json())
          .then(data => {
            // スコアボードを更新
            updateScoreBoard(data.frames);

            throwBtn.textContent = 'ボールを投げる';
            if (throwBtn.classList.contains('game-over')) {
              throwBtn.classList.remove('game-over');
            }
            throwBtn.disabled = false; // 投げるボタンを有効化
          })
      })

      // スコアボードをHTMLに描画
      const updateScoreBoard = (frames) => {
        frames.forEach((frame, frameIndex) => {
          throwScore.children[(frameIndex + 1) * 2 - 2].innerHTML = frame.firstThrow

          throwScore.children[(frameIndex + 1) * 2 - 1].innerHTML = frame.secondThrow;

          if (frameIndex === 9) {
            throwScore.children[(frameIndex + 1) * 2].innerHTML = frame.thirdThrow;
          };

          totalScore.children[frameIndex].innerHTML = frame.total;
        });
      };
      // ビジュアルアニメーションに何本倒したか描画
      const resultScoreVisual = (frames) => {
        const notNullScoreFrame = frames.filter((frame, frameIndex) => {
          if (frame.firstThrow !== null || frame.secondThrow !== null || frame.thirdThrow !== null) {
            return frame;
          }
        });
        const latestFrameScore = Object.values(notNullScoreFrame[notNullScoreFrame.length - 1]).filter((score,
          scoreIndex) => {
          return scoreIndex < 3 && score !== null;
        });

        const latestScore = latestFrameScore[latestFrameScore.length - 1];

        if (notNullScoreFrame.length === 10) {
          if (latestFrameScore[0] === 10 && !latestFrameScore[1] ||
            latestFrameScore[0] === 10 && latestFrameScore[1] === 10 ||
            latestFrameScore[2] === 10) {
            bowlingResultText.innerHTML = "ストライク!!";
          } else if (latestFrameScore[0] !== 10 &&
            latestFrameScore[0] + latestFrameScore[1] === 10 && !latestFrameScore[2] ||
            latestFrameScore[1] + latestFrameScore[2] === 10) {
            bowlingResultText.innerHTML = "スペア!!";
          } else {
            bowlingResultText.innerHTML = `<span class="pin-num">${latestScore}</span>本ヒット`;
          }
        } else {
          if (latestFrameScore[0] === 10 ||
            latestFrameScore[0] === 10) {
            bowlingResultText.innerHTML = "ストライク!!";
          } else if (latestFrameScore[0] + latestFrameScore[1] === 10) {
            bowlingResultText.innerHTML = "スペア!!";
          } else {
            bowlingResultText.innerHTML = `<span class="pin-num">${latestScore}</span>本ヒット`;
          }
        }

      };
    });
  </script>
</body>

</html>