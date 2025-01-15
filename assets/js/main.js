window.addEventListener("load", () => {
  const elements = {
    throwBtn: document.getElementById("throw-btn"),
    resetBtn: document.getElementById("reset-btn"),
    throwScore: document.querySelector("#score-board .throw-score"),
    totalScore: document.querySelector("#score-board .total-score"),
    bowlingVisual: document.getElementById("bowling-visual"),
    resultText: document.querySelector(".bowling-result p"),
  };

  const API_ENDPOINTS = {
    GET_GAME_DATA: "./api/getGameData.php",
    THROW_BALL: "./api/throwBall.php",
    RESET_GAME: "./api/resetGame.php",
  };

  const FRAME_COUNT = 10;

  // 共通のAPI呼び出し関数
  const apiRequest = (url, method = "POST") =>
    fetch(url, { method }).then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    });

  // アニメーション制御
  const toggleAnimation = (visualElement) => {
    visualElement.classList.remove("is-animation");
    setTimeout(() => visualElement.classList.add("is-animation"), 200);
  };

  // スコアボードを更新
  const updateScoreBoard = (frames) => {
    frames.forEach((frame, index) => {
      const offset = index * 2;
      elements.throwScore.children[offset].innerHTML = frame.firstThrow;
      elements.throwScore.children[offset + 1].innerHTML = frame.secondThrow;
      if (index === FRAME_COUNT - 1) {
        elements.throwScore.children[offset + 2].innerHTML = frame.thirdThrow;
      }
      elements.totalScore.children[index].innerHTML = frame.total;
    });
  };

  // 投球結果をビジュアルに描画
  const showResultVisual = (frames) => {
    const lastFrame = frames.findLast(
      (frame) =>
        frame.firstThrow !== null ||
        frame.secondThrow !== null ||
        frame.thirdThrow !== null
    );

    if (!lastFrame) return;

    const scores = [
      lastFrame.firstThrow,
      lastFrame.secondThrow,
      lastFrame.thirdThrow,
    ].filter((score) => score !== null);

    const latestScore = scores.at(-1);

    if (
      (lastFrame.firstThrow === 10 && lastFrame.secondThrow === null) ||
      (lastFrame.firstThrow === 10 && lastFrame.secondThrow === 10) ||
      lastFrame.thirdThrow === 10
    ) {
      elements.resultText.innerHTML = "ストライク!!";
    } else if (
      (lastFrame.firstThrow + lastFrame.secondThrow === 10 &&
        lastFrame.thirdThrow === null) ||
      lastFrame.secondThrow + lastFrame.thirdThrow === 10
    ) {
      elements.resultText.innerHTML = "スペア!!";
    } else {
      elements.resultText.innerHTML = `<span class="pin-num">${latestScore}</span>本ヒット`;
    }
  };

  elements.bowlingVisual.addEventListener("click", () => {
    // アニメーション
    if (elements.bowlingVisual.classList.contains("is-animation")) {
      elements.bowlingVisual.classList.remove("is-animation");
    }
  });

  // 初期データのロード
  apiRequest(API_ENDPOINTS.GET_GAME_DATA)
    .then((data) => {
      updateScoreBoard(data.frames);
      if (data.isGameOver) {
        elements.throwBtn.textContent = "ゲーム終了";
        elements.throwBtn.classList.add("game-over");
        elements.throwBtn.disabled = true;
      }
    })
    .catch((error) => {
      console.log("エラーが発生しました:", error);
    });

  // イベントリスナー
  elements.throwBtn.addEventListener("click", () => {
    toggleAnimation(elements.bowlingVisual);

    apiRequest(API_ENDPOINTS.THROW_BALL)
      .then((data) => {
        showResultVisual(data.frames);
        setTimeout(() => updateScoreBoard(data.frames), 1300);
        if (data.isGameOver) {
          elements.throwBtn.textContent = "ゲーム終了";
          elements.throwBtn.classList.add("game-over");
          elements.throwBtn.disabled = true;
        }
      })
      .catch((error) => {
        console.log("エラーが発生しました:", error);
      });
  });

  elements.resetBtn.addEventListener("click", () => {
    apiRequest(API_ENDPOINTS.RESET_GAME)
      .then((data) => {
        updateScoreBoard(data.frames);
        elements.throwBtn.textContent = "ボールを投げる";
        if (elements.throwBtn.classList.contains("game-over")) {
          elements.throwBtn.classList.remove("game-over");
        }
        elements.throwBtn.disabled = false;
      })
      .catch((error) => {
        console.log("エラーが発生しました:", error);
      });
  });
});
