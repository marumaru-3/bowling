<?php

// ボウリングピン
$totalPins = 10;
// 現在のフレーム
$currentFrame = 1;
// 現在の投球回数
$currentThrow = 1;
// スコアボード
$score = [];
// 各投球結果を記録
$currentThrowPins = $totalPins;
// ゲーム終了フラグ
$isGameOver = false;

// 1投ごとの処理の関数
function throwOnce()
{
  global $totalPins,
    $currentFrame,
    $currentThrow,
    $score,
    $currentThrowPins,
    $isGameOver;

  // 投球結果
  // $throw = random_int(0, $currentThrowPins);
  $throw = 10;

  // フレーム結果の初期化
  if (!isset($score[$currentFrame])) {
    // 10フレーム目の場合
    if ($currentFrame === 10) {
      $score[$currentFrame] = [
        "firstThrow" => null,
        "secondThrow" => null,
        "thirdThrow" => null,
        "total" => null,
      ];
    }
    // 10フレーム目より前の場合
    else {
      $score[$currentFrame] = [
        "firstThrow" => null,
        "secondThrow" => null,
        "total" => null,
      ];
    }
  }

  // 10フレーム目の特殊処理
  if ($currentFrame === 10) {
    if ($currentThrow === 1) {
      // 1投目
      $currentThrowPins -= $throw;
      $score[$currentFrame]["firstThrow"] = $throw;

      if ($throw === $totalPins) {
        // ストライクの場合
        // 投球結果をリセットして2投目へ
        $currentThrowPins = $totalPins;
        $currentThrow++;
      } else {
        // ストライク以外の場合
        // 2投目へ
        $currentThrow++;
      }
    } elseif ($currentThrow === 2) {
      // 2投目
      $currentThrowPins -= $throw;
      $score[$currentFrame]["secondThrow"] = $throw;

      if (
        $throw === $totalPins ||
        $score[$currentFrame]["firstThrow"] + $throw === $totalPins
      ) {
        // 2投目もストライクだった場合または2投目がスペアだった場合
        // 投球結果をリセットして3投目へ
        $currentThrowPins = $totalPins;
        $currentThrow++;

        // スコア計算
        updateScoreBoard();
      } elseif ($score[$currentFrame]["firstThrow"] === $totalPins) {
        // 1投目がストライクだった場合
        // 3投目へ
        $currentThrow++;

        // スコア計算
        updateScoreBoard();
      } else {
        // それ以外
        // スコア計算
        updateScoreBoard();

        // ゲームを終了
        $isGameOver = true;
      }
    } elseif ($currentThrow === 3) {
      // 3投目
      $currentThrowPins -= $throw;
      $score[$currentFrame]["thirdThrow"] = $throw;
      // スコア計算
      updateScoreBoard();

      // ゲームを終了
      $isGameOver = true;
    }
  }
  // 1～9フレーム目までの通常処理
  else {
    // 1投目
    if ($currentThrow === 1) {
      $currentThrowPins -= $throw;
      $score[$currentFrame]["firstThrow"] = $throw;

      if ($throw === 10) {
        // ストライクの場合
        // 2投目をスキップ
        $score[$currentFrame]["secondThrow"] = 0;
        $currentThrow = 1;
        $currentThrowPins = $totalPins;
        // スコア計算
        updateScoreBoard();
        // 次のフレームへ
        $currentFrame++;
      } else {
        // ストライク以外の場合
        // 2投目へ
        $currentThrow++;
      }
    }
    // 2投目
    elseif ($currentThrow === 2) {
      $score[$currentFrame]["secondThrow"] = $throw;
      // 投球回数と結果を初期値に戻す
      $currentThrow = 1;
      $currentThrowPins = $totalPins;
      // スコア計算
      updateScoreBoard();
      // 次のフレームへ
      $currentFrame++;
    }
  }
}

// 各フレームのスコア計算関数
function updateScoreBoard()
{
  global $totalPins, $score;

  $scoreTotal = 0;

  foreach ($score as $key => $frame) {
    // 共通部分
    $scoreTotal += $frame["firstThrow"] + $frame["secondThrow"];
    // 10フレーム目の処理
    if ($key === 10 && isset($frame['thirdThrow'])) {
      $scoreTotal += $frame["thirdThrow"];
    }
    // 1～9フレーム目でストライクまたはスペアの場合
    elseif (
      $frame["firstThrow"] === $totalPins ||
      $frame["firstThrow"] + $frame["secondThrow"] === $totalPins
    ) {
      // 次のスコアが記録されている場合
      if (isset($score[$key + 1])) {
        $scoreTotal += $score[$key + 1]["firstThrow"];

        // ストライクの場合のみ
        if ($frame["firstThrow"] === $totalPins) {
          // 次もストライクの場合
          if ($score[$key + 1]["firstThrow"] === $totalPins) {
            // その次のスコアが記録されている場合
            if (isset($score[$key + 2])) {
              $scoreTotal += $score[$key + 2]["firstThrow"];
            }
            // 9フレーム目の場合
            elseif (
              $key === 9 &&
              isset($score[$key + 1]["secondThrow"])
            ) {
              $scoreTotal += $score[$key + 1]["secondThrow"];
            }
            // その次のスコアが記録されていない場合
            else {
              // スコア計算をスキップ
              continue;
            }
          }
          // 次もストライクでない場合
          else {
            $scoreTotal += $score[$key + 1]["secondThrow"];
          }
        }
      }
      // 次のスコアが記録されていない場合
      else {
        // スコア計算をスキップ
        continue;
      }
    }

    // スコア合計を該当フレームに追加
    $score[$key]["total"] = $score[$key]["total"] ?? $scoreTotal;
  }
}

// 関数の繰り返し処理
while (!$isGameOver) {
  throwOnce();
}

// スコアボードの配列を画面に表示
print_r($score);
