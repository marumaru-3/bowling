<?php
// セッションの開始
session_start();

// セッションからゲームデータを削除してリセット
unset($_SESSION["game"]);

// BowlingGameクラスファイルの読み込み
require_once "../models/BowlingGame.php";

// 新しいゲームを作成してセッションに保存
$scoreCalculator = new ScoreCalculator();
$game = new BowlingGame($scoreCalculator);
$_SESSION["game"] = serialize($game);

// スコアボードデータをJSON形式で返す
header("Content-Type: application/json");
echo json_encode([
    "isGameOver" => $game->isGameOver,
    "frames" => $game->getScoreBoard(),
]);
