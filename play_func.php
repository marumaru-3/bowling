<?php

// ボウリングピン
$totalPins = 10;
// 現在のフレーム
$currentFrame = 1;
// 現在の投球回数
$currentThrow = 1;
// スコアボード
$score = scoreBoardCreate();
// 各投球結果を記録
$currentThrowPins = $totalPins;
// ゲーム終了フラグ
$isGameOver = false;

// スコアボードの配列を初期化
function scoreBoardCreate($totalFrames = 10)
{
    $arr = [];
    for ($i = 1; $i <= $totalFrames; $i++) {
        // フレーム結果の初期化
        // 10フレーム目の場合
        if ($i === $totalFrames) {
            $arr[$i] = [
                "firstThrow" => null,
                "secondThrow" => null,
                "thirdThrow" => null,
                "total" => null,
            ];
        }
        // 10フレーム目より前の場合
        else {
            $arr[$i] = [
                "firstThrow" => null,
                "secondThrow" => null,
                "total" => null,
            ];
        }
    }

    return $arr;
}

// 投球結果を取得する関数
function getThrowResult($currentThrowPins)
{
    return random_int(0, $currentThrowPins);
}

// 投球結果の記録
function recordThrow($currentThrow, &$frame, $throwResult)
{
    // 1投目の場合
    if ($currentThrow === 1) {
        $frame["firstThrow"] = $throwResult;
    }
    // 2投目の場合
    elseif ($currentThrow === 2) {
        $frame["secondThrow"] = $throwResult;
    }
    // 3投目の場合
    elseif ($currentThrow === 3) {
        $frame["thirdThrow"] = $throwResult;
    }
}

// 次の投球やフレームへの進行管理
function nextThrowOrFrame($throwResult)
{
    global $totalPins, $currentFrame, $currentThrow, $score, $currentThrowPins;
    // 1投目
    if ($currentThrow === 1) {
        if ($throwResult === 10) {
            // ストライクの場合
            // 2投目をスキップ
            $score[$currentFrame]["secondThrow"] = 0;
            $currentThrow = 1;
            $currentThrowPins = $totalPins;
            // スコア計算
            updateScoreBoard($totalPins, $score);
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
        // 投球回数と結果を初期値に戻す
        $currentThrow = 1;
        $currentThrowPins = $totalPins;
        // スコア計算
        updateScoreBoard($totalPins, $score);
        // 次のフレームへ
        $currentFrame++;
    }
}

// 10フレーム目の特殊処理
function handleFinalFrame($throwResult)
{
    global $totalPins, $currentFrame, $currentThrow, $score, $currentThrowPins;

    // 1投目
    if ($currentThrow === 1) {
        if ($throwResult === $totalPins) {
            // ストライクの場合
            // 投球結果をリセットして2投目へ
            $currentThrowPins = $totalPins;
            $currentThrow++;
        } else {
            // ストライク以外の場合
            // 2投目へ
            $currentThrow++;
        }
    }
    // 2投目
    elseif ($currentThrow === 2) {
        if (
            $throwResult === $totalPins ||
            $score[$currentFrame]["firstThrow"] + $throwResult === $totalPins
        ) {
            // 2投目もストライクだった場合または2投目がスペアだった場合
            // 投球結果をリセットして3投目へ
            $currentThrowPins = $totalPins;
            $currentThrow++;
        } elseif ($score[$currentFrame]["firstThrow"] === $totalPins) {
            // 1投目がストライクだった場合
            // 3投目へ
            $currentThrow++;
        } else {
            // ストライクやスペア以外
            // 3投目をスキップ
            $score[$currentFrame]["thirdThrow"] = 0;
            $currentThrow = 1;
            $currentThrowPins = $totalPins;
        }

        // スコア計算
        updateScoreBoard($totalPins, $score);
    }
    // 3投目
    elseif ($currentThrow === 3) {
        // スコア計算
        updateScoreBoard($totalPins, $score);
    }
}

// ゲーム終了条件の管理
function checkGameOver()
{
    global $currentFrame, $score, $isGameOver;
    if ($currentFrame === 10 && isset($score[$currentFrame]["thirdThrow"])) {
        // ゲームを終了
        $isGameOver = true;
    }
}

// 投球結果を更新する関数
function throwOnce()
{
    global $currentFrame, $currentThrow, $score, $currentThrowPins;

    // 投球結果
    $throwResult = getThrowResult($currentThrowPins);
    // $throwResult = 10;

    // 参照フレーム
    $frame = &$score[$currentFrame];

    // 残りのピン数
    $currentThrowPins -= $throwResult;
    // 投球結果を記録
    recordThrow($currentThrow, $frame, $throwResult);

    // 10フレーム目の特殊処理
    if ($currentFrame === 10) {
        handleFinalFrame($throwResult);
    }
    // 1～9フレーム目までの通常処理
    else {
        nextThrowOrFrame($throwResult);
    }

    checkGameOver();
}

// ここから計算
// 各フレームのスコア計算
function calcBaseFrameScore($frameNumber, $score)
{
    $scoreTotal =
        $score[$frameNumber]["firstThrow"] +
        $score[$frameNumber]["secondThrow"];

    return $scoreTotal;
}

// ストライクのボーナス計算
function calcStrikeBonus($frameNumber, $score, $totalPins)
{
    // 連続ストライクの場合
    if ($score[$frameNumber + 1]["firstThrow"] === $totalPins) {
        // その次のスコアが記録されている場合
        if (isset($score[$frameNumber + 2]["firstThrow"])) {
            $bonusScore = $score[$frameNumber + 2]["firstThrow"];
        }
        // その次のスコアが存在しない場合
        elseif (!isset($score[$frameNumber + 2])) {
            $bonusScore = $score[$frameNumber + 1]["secondThrow"];
        }
        // その次のスコアが記録されていない場合
        else {
            $bonusScore = null;
        }
    }
    // 次もストライクでない場合
    else {
        $bonusScore = $score[$frameNumber + 1]["secondThrow"];
    }

    return $bonusScore;
}

// スペアのボーナス計算
function calcSpareBonus($frameNumber, $score)
{
    $bonusScore = $score[$frameNumber + 1]["firstThrow"];

    return $bonusScore;
}

// 10フレーム目の特殊計算
function calcFinalFrameScore($frameNumber, $score)
{
    $scoreTotal =
        calcBaseFrameScore($frameNumber, $score) +
        $score[$frameNumber]["thirdThrow"];

    return $scoreTotal;
}

// 各フレームの合計スコアを計算する関数
function updateScoreBoard($totalPins, &$score)
{
    $scoreTotal = 0;

    foreach ($score as $frameNumber => &$frame) {
        // 10フレーム目の特殊処理
        if ($frameNumber === 10) {
            if (isset($frame["thirdThrow"])) {
                $scoreTotal += calcFinalFrameScore($frameNumber, $score);
            } else {
                $scoreTotal = null;
            }
        }

        // 1～9フレーム目の処理
        if ($frameNumber < 10) {
            if (isset($frame["firstThrow"]) && isset($frame["secondThrow"])) {
                $scoreTotal += calcBaseFrameScore($frameNumber, $score);
            } else {
                $scoreTotal = null;
            }

            // ストライクの場合
            if ($frame["firstThrow"] === $totalPins) {
                // 次の1.2投目が記録されている場合
                if (
                    isset($score[$frameNumber + 1]["firstThrow"]) &&
                    isset($score[$frameNumber + 1]["secondThrow"])
                ) {
                    $scoreTotal += calcSpareBonus($frameNumber, $score);

                    $scoreTotal =
                        calcStrikeBonus($frameNumber, $score, $totalPins) !==
                        null
                            ? $scoreTotal +
                                calcStrikeBonus(
                                    $frameNumber,
                                    $score,
                                    $totalPins
                                )
                            : null;
                }
                // 記録されていない場合
                else {
                    $scoreTotal = null;
                }
            }
            // スペアの場合
            elseif (
                $frame["firstThrow"] + $frame["secondThrow"] ===
                $totalPins
            ) {
                // 次の1投目が記録されている場合
                if (isset($score[$frameNumber + 1]["firstThrow"])) {
                    $scoreTotal += calcSpareBonus($frameNumber, $score);
                }
                // 記録されていない場合
                else {
                    $scoreTotal = null;
                }
            }
        }

        // スコア合計を該当フレームに追加
        $frame["total"] = $frame["total"] ?? $scoreTotal;
    }
}

// 関数の繰り返し処理
while (!$isGameOver) {
    throwOnce();
}

// スコアボードの配列を画面に表示
print_r($score);
