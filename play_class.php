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

    // ストライクの判定
    public function isStrike()
    {
        return $this->firstThrow === 10;
    }

    // スペアの判定
    public function isSpare()
    {
        return $this->firstThrow + $this->secondThrow === 10;
    }
}

// ゲーム全体の状態を管理し、スコア計算や投球の進行を統括
class BowlingGame
{
    private $frames = [];
    private $currentFrameIndex = 0;
    private $currentThrow = 1;
    public $isGameOver = false;

    public function __construct($totalFrames = 10)
    {
        for ($i = 0; $i < $totalFrames; $i++) {
            $this->frames[] = new Frame();
        }
    }

    public function throwBall($pins)
    {
        $currentFrame = $this->frames[$this->currentFrameIndex];
        $currentFrame->recordThrow($this->currentThrow, $pins);

        if ($this->currentFrameIndex === 9) {
            $this->handleFinalFrame($pins);
        } else {
            $this->nextThrowOrFrame($pins);
        }

        $this->updateScore();
        $this->checkGameOver();
    }

    private function nextThrowOrFrame($pins)
    {
        if ($this->currentThrow === 1 && $pins === 10) {
            // ストライク
            $this->currentThrow = 1;
            $this->currentFrameIndex++;
        } elseif ($this->currentThrow === 1) {
            // ストライク以外の場合
            $this->currentThrow++;
        } else {
            // 2投目
            $this->currentThrow = 1;
            $this->currentFrameIndex++;
        }
    }

    // 10フレーム目の特殊処理を実装
    private function handleFinalFrame($pins)
    {
    }

    // スコア計算を実装
    private function updateScore()
    {
    }

    // ゲーム終了条件
    private function checkGameOver()
    {
        if (
            $this->currentFrameIndex === 9 &&
            $this->frames[9]->getScore()["thirdThrow"] !== null
        ) {
            $this->isGameOver = true;
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
    $pins = random_int(0, 10);
    $game->throwBall($pins);
}

print_r($game->getScoreBoard());
