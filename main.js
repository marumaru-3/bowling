window.addEventListener("load", () => {
  const throwBtn = document.getElementById("throw-btn");
  const resetBtn = document.getElementById("reset-btn");
  const throwScore = document.querySelector("#score-board .throw-score");
  const totalScore = document.querySelector("#score-board .total-score");
  const bowlingVisual = document.getElementById("bowling-visual");
  const bowlingResultText = document.querySelector(".bowling-result p");

  // 最初にスコアボードを表示
  fetch("getGameData.php", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      // スコアボードを更新
      updateScoreBoard(data.frames);

      // ゲーム終了判定
      if (data.isGameOver) {
        throwBtn.textContent = "ゲーム終了";
        throwBtn.classList.add("game-over");
        throwBtn.disabled = true; // ボタンを無効化
      }
    })
    .catch((error) => {
      console.log("エラーが発生しました:", error);
    });

  bowlingVisual.addEventListener("click", () => {
    // アニメーション
    if (bowlingVisual.classList.contains("is-animation")) {
      bowlingVisual.classList.remove("is-animation");
    }
  });

  throwBtn.addEventListener("click", () => {
    // ビジュアル画面までスクロール
    const bowlingVisualDOMRect = bowlingVisual.getBoundingClientRect();
    const bowlingVisualTop = bowlingVisualDOMRect.top + window.pageYOffset;
    window.scrollTo({
      top: bowlingVisualTop,
      behavior: "smooth",
    });

    // アニメーション
    if (bowlingVisual.classList.contains("is-animation")) {
      bowlingVisual.classList.remove("is-animation");
      setTimeout(() => {
        bowlingVisual.classList.add("is-animation");
      }, 200);
    } else {
      bowlingVisual.classList.add("is-animation");
    }

    // 投球リクエストを送信
    // PHPファイルにPOSTリクエストを送る
    fetch("throwBall.php", {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        // ビジュアルアニメーションに投球結果を表示
        resultScoreVisual(data.frames);
        // スコアボードを更新
        setTimeout(() => {
          updateScoreBoard(data.frames);
        }, 1300);

        // ゲーム終了判定
        if (data.isGameOver) {
          throwBtn.textContent = "ゲーム終了";
          throwBtn.classList.add("game-over");
          throwBtn.disabled = true; // ボタンを無効化
        }
      })
      .catch((error) => {
        console.log("エラーが発生しました:", error);
      });
  });

  // ゲームリセットリクエストを送信
  resetBtn.addEventListener("click", () => {
    // アニメーション
    bowlingVisual.classList.remove("is-animation");

    // PHPファイルにPOSTリクエストを送る
    fetch("resetGame.php", {
      method: "POST",
    })
      .then((response) => response.json())
      .then((data) => {
        // スコアボードを更新
        updateScoreBoard(data.frames);

        throwBtn.textContent = "ボールを投げる";
        if (throwBtn.classList.contains("game-over")) {
          throwBtn.classList.remove("game-over");
        }
        throwBtn.disabled = false; // 投げるボタンを有効化
      });
  });

  // スコアボードをHTMLに描画
  const updateScoreBoard = (frames) => {
    frames.forEach((frame, frameIndex) => {
      throwScore.children[(frameIndex + 1) * 2 - 2].innerHTML =
        frame.firstThrow;

      throwScore.children[(frameIndex + 1) * 2 - 1].innerHTML =
        frame.secondThrow;

      if (frameIndex === 9) {
        throwScore.children[(frameIndex + 1) * 2].innerHTML = frame.thirdThrow;
      }

      totalScore.children[frameIndex].innerHTML = frame.total;
    });
  };
  // ビジュアルアニメーションに何本倒したか描画
  const resultScoreVisual = (frames) => {
    const notNullScoreFrame = frames.filter((frame, frameIndex) => {
      if (
        frame.firstThrow !== null ||
        frame.secondThrow !== null ||
        frame.thirdThrow !== null
      ) {
        return frame;
      }
    });
    const latestFrameScore = Object.values(
      notNullScoreFrame[notNullScoreFrame.length - 1]
    ).filter((score, scoreIndex) => {
      return scoreIndex < 3 && score !== null;
    });

    const latestScore = latestFrameScore[latestFrameScore.length - 1];

    if (notNullScoreFrame.length === 10) {
      if (
        (latestFrameScore[0] === 10 && !latestFrameScore[1]) ||
        (latestFrameScore[0] === 10 && latestFrameScore[1] === 10) ||
        latestFrameScore[2] === 10
      ) {
        bowlingResultText.innerHTML = "ストライク!!";
      } else if (
        (latestFrameScore[0] !== 10 &&
          latestFrameScore[0] + latestFrameScore[1] === 10 &&
          !latestFrameScore[2]) ||
        latestFrameScore[1] + latestFrameScore[2] === 10
      ) {
        bowlingResultText.innerHTML = "スペア!!";
      } else {
        bowlingResultText.innerHTML = `<span class="pin-num">${latestScore}</span>本ヒット`;
      }
    } else {
      if (latestFrameScore[0] === 10 || latestFrameScore[0] === 10) {
        bowlingResultText.innerHTML = "ストライク!!";
      } else if (latestFrameScore[0] + latestFrameScore[1] === 10) {
        bowlingResultText.innerHTML = "スペア!!";
      } else {
        bowlingResultText.innerHTML = `<span class="pin-num">${latestScore}</span>本ヒット`;
      }
    }
  };
});
