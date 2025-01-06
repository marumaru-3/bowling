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

// 1投ごとの処理の関数
function throwOnce()
{
    global $totalPins, $currentFrame, $currentThrow, $score, $currentThrowPins;

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

    if ($currentThrow === 1) {
        // 1投目
        $currentThrowPins -= $throw;
        $score[$currentFrame]["firstThrow"] = $throw;

        if ($throw === 10) {
            // ストライクの場合
            if ($currentFrame === 10) {
                // 10フレーム目の場合
                // 2投目へ
                $currentThrow++;
            } else {
                // 10フレーム目以外の場合
                // 2投目をスキップして次のフレームへ
                $currentThrowPins = $totalPins;
                $score[$currentFrame]["secondThrow"] = $throw;
                $currentFrame++;
            }
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
        if ($currentFrame < 10) {
            $currentFrame++;
        }
    }
}

throwOnce();
throwOnce();
throwOnce();
throwOnce();

// // 10フレームまでのスコアを作成
// for ($i = 0; $i < 10; $i++) {
//     // 1フレームに二度投げる
//     // 最初に倒した本数
//     $firstThrow = rand(0, $totalPins);
//     // 連続ストライク検証用
//     // $firstThrow = 10;
//     // 一度目がストライクじゃなかった場合、残りの数からピンを倒す
//     $secondThrow =
//         $firstThrow !== 10 ? rand(0, $totalPins - $firstThrow) : null;
//     // 10フレーム目用
//     $thirdThrow = null;

//     // 10フレーム目の処理
//     if ($i === 9) {
//         // ストライクの場合
//         if ($firstThrow === $totalPins) {
//             $secondThrow = rand(0, $totalPins);
//             $thirdThrow =
//                 $secondThrow === 10
//                     ? rand(0, $totalPins)
//                     : rand(0, $totalPins - $secondThrow);
//         }
//         // スペアの場合
//         elseif ($firstThrow + $secondThrow === $totalPins) {
//             $thirdThrow = rand(0, $totalPins);
//         }
//     }

//     // 投球結果をスコアボードに記録
//     $score[] = [
//         "firstThrow" => $firstThrow,
//         "secondThrow" => $secondThrow,
//         "thirdThrow" => $thirdThrow,
//         "total" => 0,
//     ];
// }

// // 各フレームのスコア合計を計算
// for ($i = 0; $i < 10; $i++) {
//     $frame = &$score[$i];

//     // 現在のフレームのスコア
//     if ($i === 9) {
//         // 10フレーム目
//         $frame["total"] =
//             $frame["firstThrow"] +
//             ($frame["secondThrow"] ?? 0) +
//             ($frame["thirdThrow"] ?? 0);
//     } else {
//         // 1～9フレーム目
//         $frame["total"] = $frame["firstThrow"] + ($frame["secondThrow"] ?? 0);
//     }

//     // ストライクの場合
//     if ($frame["firstThrow"] === $totalPins) {
//         if ($i + 1 < 10) {
//             $frame["total"] += $score[$i + 1]["firstThrow"];

//             // 次のフレームもストライクの場合
//             if ($score[$i + 1]["firstThrow"] === 10 && $i + 2 < 10) {
//                 $frame["total"] += $score[$i + 2]["firstThrow"];
//             } else {
//                 $frame["total"] += $score[$i + 1]["secondThrow"];
//             }
//         }
//     }
//     // スペアの場合
//     elseif (
//         $frame["firstThrow"] + ($frame["secondThrow"] ?? 0) ===
//         $totalPins
//     ) {
//         if ($i + 1 < 10) {
//             $frame["total"] += $score[$i + 1]["firstThrow"];
//         }
//     }

//     // 前のフレームのスコアを足す
//     if ($i > 0) {
//         $frame["total"] += $score[$i - 1]["total"];
//     }
// }

// スコアボードの配列を画面に表示
print_r($score);
