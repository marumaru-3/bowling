<?php

// 各フレームの状態（投球結果、スコア）を管理
class Frame
{
    private $firstThrow = null;
    private $secondThrow = null;
    private $thirdThrow = null; // 10フレーム目専用
    private $total = null;

    // 投球結果の記録
    public function recordThrow($throwNumber, $pins)
    {
        if ($throwNumber === 1) {
            $this->firstThrow = $pins;
        } elseif ($throwNumber === 2) {
            $this->secondThrow = $pins;
        } elseif ($throwNumber === 3) {
            $this->thirdThrow = $pins;
        }
    }

    // 投球結果を配列に格納
    public function getScore()
    {
        return [
            "firstThrow" => $this->firstThrow,
            "secondThrow" => $this->secondThrow,
            "thirdThrow" => $this->thirdThrow,
            "total" => $this->total,
        ];
    }

    // フレームごとの累積スコアを保存
    public function setTotal($total)
    {
        $this->total = $total;
    }
}

// スコア計算用クラス
class ScoreCalculator
{
    // 各フレームの合計スコアを計算する関数
    public function updateScore(&$frames)
    {
        $totalPins = 10;
        $scoreTotal = 0;

        foreach ($frames as $frameIndex => &$frame) {
            // 10フレーム目の特殊処理
            if ($frameIndex === 10) {
                if (isset($frame["thirdThrow"])) {
                    $scoreTotal += $this->calcFinalFrameScore($frameIndex, $frames);
                } else {
                    $scoreTotal = null;
                }
            }

            // 1～9フレーム目の処理
            if ($frameIndex < 10) {
                if (isset($frame["firstThrow"]) && isset($frame["secondThrow"])) {
                    $scoreTotal += $this->calcBaseFrameScore($frameIndex, $frames);
                } else {
                    $scoreTotal = null;
                }

                // ストライクの場合
                if ($frame["firstThrow"] === $totalPins) {
                    // 次の1.2投目が記録されている場合
                    if (
                        isset($frames[$frameIndex + 1]["firstThrow"]) &&
                        isset($frames[$frameIndex + 1]["secondThrow"])
                    ) {
                        $scoreTotal += $this->calcSpareBonus($frameIndex, $frames);

                        $scoreTotal =
                            $this->calcStrikeBonus($frameIndex, $frames, $totalPins) !==
                            null
                            ? $scoreTotal +
                            $this->calcStrikeBonus(
                                $frameIndex,
                                $frames,
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
                    if (isset($frames[$frameIndex + 1]["firstThrow"])) {
                        $scoreTotal += $this->calcSpareBonus($frameIndex, $frames);
                    }
                    // 記録されていない場合
                    else {
                        $scoreTotal = null;
                    }
                }
            }

            // スコア合計を該当フレームに追加
            return $frame["total"] = $frame["total"] ?? $scoreTotal;
        }
    }

    // 各フレームのスコア計算
    private  function calcBaseFrameScore($frameIndex, $frames)
    {
        $scoreTotal =
            $frames[$frameIndex]["firstThrow"] +
            $frames[$frameIndex]["secondThrow"];

        return $scoreTotal;
    }

    // ストライクのボーナス計算
    private function calcStrikeBonus($frameIndex, $frames, $totalPins)
    {
        // 連続ストライクの場合
        if ($frames[$frameIndex + 1]["firstThrow"] === $totalPins) {
            // その次のスコアが記録されている場合
            if (isset($frames[$frameIndex + 2]["firstThrow"])) {
                $bonusScore = $frames[$frameIndex + 2]["firstThrow"];
            }
            // その次のスコアが存在しない場合
            elseif (!isset($frames[$frameIndex + 2])) {
                $bonusScore = $frames[$frameIndex + 1]["secondThrow"];
            }
            // その次のスコアが記録されていない場合
            else {
                $bonusScore = null;
            }
        }
        // 次もストライクでない場合
        else {
            $bonusScore = $frames[$frameIndex + 1]["secondThrow"];
        }

        return $bonusScore;
    }

    // スペアのボーナス計算
    private function calcSpareBonus($frameIndex, $frames)
    {
        $bonusScore = $frames[$frameIndex + 1]["firstThrow"];

        return $bonusScore;
    }

    // 10フレーム目の特殊計算
    private function calcFinalFrameScore($frameIndex, $frames)
    {
        $scoreTotal =
            $this->calcBaseFrameScore($frameIndex, $frames) +
            $frames[$frameIndex]["thirdThrow"];

        return $scoreTotal;
    }
}

// ゲーム全体の状態を管理し、スコア計算や投球の進行を統括
class BowlingGame
{
    private $frames = [];
    private $currentFrameIndex = 0;
    private $currentThrow = 1;
    private $currentRemainingPins = 10;
    public $isGameOver = false;

    public function __construct($totalFrames = 10)
    {
        for ($i = 0; $i < $totalFrames; $i++) {
            $this->frames[] = new Frame();
        }
    }

    // 投球の処理
    public function throwBall()
    {
        // 投球結果
        // $throwResult = 10;
        $throwResult = random_int(0, $this->currentRemainingPins);

        // 残りのピン数を記録
        $this->currentRemainingPins -= $throwResult;

        $currentFrame = $this->frames[$this->currentFrameIndex];
        $currentFrame->recordThrow($this->currentThrow, $throwResult);


        if ($this->currentFrameIndex === 9) {
            $this->handleFinalFrame($throwResult);
        } else {
            $this->nextThrowOrFrame($throwResult);
        }

        $this->updateScore($this->frames);
        $this->checkGameOver();
    }

    // 次の投球か次のフレームに進めるか判定
    private function nextThrowOrFrame($throwResult)
    {
        if ($this->currentThrow === 1 && $throwResult === 10) {
            // ストライク
            $this->currentThrow = 1;
            $this->currentRemainingPins = 10;
            $this->currentFrameIndex++;
        } elseif ($this->currentThrow === 1) {
            // ストライク以外の場合
            $this->currentThrow++;
        } else {
            // 2投目
            $this->currentThrow = 1;
            $this->currentRemainingPins = 10;
            $this->currentFrameIndex++;
        }
    }

    // 10フレーム目の特殊処理を実装
    private function handleFinalFrame($throwResult)
    {
        $currentFrame = $this->frames[$this->currentFrameIndex];

        // 1投目
        if ($this->currentThrow === 1) {
            if ($throwResult === 10) {
                // ストライク
                $this->currentRemainingPins = 10;
                $this->currentThrow++;
            } else {
                // ストライク以外の場合
                $this->currentThrow++;
            }
            return;
        }
        // 2投目
        if ($this->currentThrow === 2) {
            $currentScores = $currentFrame->getScore();

            if ($currentScores['firstThrow'] + $throwResult === 10) {
                // スペア
                $this->currentRemainingPins = 10;
                $this->currentThrow++;
            } else if ($currentScores['firstThrow'] === 10) {
                // 1投目がストライクだった場合
                $this->currentThrow++;
            }
            return;
        }
    }

    // スコア計算を実装
    private function updateScore($frames)
    {
        $scoreCalculator = new ScoreCalculator();
        return $scoreCalculator->updateScore($frames);
    }

    // ゲーム終了条件
    private function checkGameOver()
    {
        $lastFrameScore = $this->frames[9]->getScore();

        if (
            $this->currentFrameIndex === 9 &&
            isset($lastFrameScore['firstThrow']) &&
            isset($lastFrameScore['secondThrow'])
        ) {
            if (
                isset($lastFrameScore['thirdThrow']) ||
                $lastFrameScore['firstThrow'] + $lastFrameScore['secondThrow'] < 10
            ) {
                $this->isGameOver = true;
            }
        }
    }

    // スコアボードに投球結果を記録
    public function getScoreBoard()
    {
        $scoreBoard = [];
        foreach ($this->frames as $frame) {
            $scoreBoard[] = $frame->getScore();
        }
        return $scoreBoard;
    }
}


// ゲーム進行コード
$game = new BowlingGame();

while (!$game->isGameOver) {
    $game->throwBall();
}

print_r($game->getScoreBoard());
