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
  global $totalPins, $currentFrame, $currentThrow, $score, $currentThrowPins, $isGameOver;

  // 投球結果
  $throw = random_int(0, $currentThrowPins);

  // フレーム結果の初期化
  if (!isset($score[$currentFrame])) {
    if ($currentFrame === 10) {
      // 10フレーム目の場合
      $score[$currentFrame] = [
        "firstThrow" => null,
        "secondThrow" => null,
        "thirdThrow" => null,
        "total" => null,
      ];
    } else {
      // 10フレーム目より前の場合
      $score[$currentFrame] = [
        "firstThrow" => null,
        "secondThrow" => null,
        "total" => null,
      ];
    }
  }

  if ($currentFrame === 10) {
    // 10フレーム目の特殊処理
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
    } else if ($currentThrow === 2) {
      // 2投目
      $score[$currentFrame]["secondThrow"] = $throw;

      if ($throw === $totalPins || $score[$currentFrame]["firstThrow"] + $throw === $totalPins) {
        // 2投目もストライクだった場合または2投目がスペアだった場合
        // 投球結果をリセットして3投目へ
        $currentThrowPins = $totalPins;
        $currentThrow++;
      } else if ($score[$currentFrame]["firstThrow"] === $totalPins) {
        // 1投目がストライクだった場合
        // 3投目へ
        $currentThrow++;
      } else {
        // それ以外
        // ゲームを終了
        $isGameOver = true;
      }
    } else if ($currentThrow === 3) {
      // 3投目
      $score[$currentFrame]["thirdThrow"] = $throw;

      // ゲームを終了
      $isGameOver = true;
    }
  } else {
    // 1～9フレーム目までの通常処理
    if ($currentThrow === 1) {
      // 1投目
      $currentThrowPins -= $throw;
      $score[$currentFrame]["firstThrow"] = $throw;

      if ($throw === 10) {
        // ストライクの場合
        // 2投目をスキップして次のフレームへ
        $score[$currentFrame]["secondThrow"] = 0;
        $currentThrow = 1;
        $currentThrowPins = $totalPins;
        $currentFrame++;
      } else {
        // ストライク以外の場合
        // 2投目へ
        $currentThrow++;
      }
    } elseif ($currentThrow === 2) {
      // 2投目
      $score[$currentFrame]["secondThrow"] = $throw;
      // 投球回数と結果を初期値に戻す
      $currentThrow = 1;
      $currentThrowPins = $totalPins;
      // 次のフレームへ
      $currentFrame++;
    }
  }
}

// スコア計算関数
function updateScoreBoard() {}

// 関数の繰り返し処理
while (!$isGameOver) {
  throwOnce();
}


// 各フレームのスコア合計を計算
// for ($i = 0; $i < 10; $i++) {
//   $frame = &$score[$i];

//   // 現在のフレームのスコア
//   if ($i === 9) {
//     // 10フレーム目
//     $frame["total"] =
//       $frame["firstThrow"] +
//       ($frame["secondThrow"] ?? 0) +
//       ($frame["thirdThrow"] ?? 0);
//   } else {
//     // 1～9フレーム目
//     $frame["total"] = $frame["firstThrow"] + ($frame["secondThrow"] ?? 0);
//   }

//   // ストライクの場合
//   if ($frame["firstThrow"] === $totalPins) {
//     if ($i + 1 < 10) {
//       $frame["total"] += $score[$i + 1]["firstThrow"];

//       // 次のフレームもストライクの場合
//       if ($score[$i + 1]["firstThrow"] === 10 && $i + 2 < 10) {
//         $frame["total"] += $score[$i + 2]["firstThrow"];
//       } else {
//         $frame["total"] += $score[$i + 1]["secondThrow"];
//       }
//     }
//   }
//   // スペアの場合
//   elseif (
//     $frame["firstThrow"] + ($frame["secondThrow"] ?? 0) ===
//     $totalPins
//   ) {
//     if ($i + 1 < 10) {
//       $frame["total"] += $score[$i + 1]["firstThrow"];
//     }
//   }

//   // 前のフレームのスコアを足す
//   if ($i > 0) {
//     $frame["total"] += $score[$i - 1]["total"];
//   }
// }

// スコアボードの配列を画面に表示
print_r($score);
