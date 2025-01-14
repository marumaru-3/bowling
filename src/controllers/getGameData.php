<?php
// セッションの開始
session_start();

// BowlingGameクラスファイルの読み込み
require_once "BowlingGame.php";

// セッションからゲームの状態を取得（初回リクエストなら新規作成）
if (!isset($_SESSION["game"])) {
    $scoreCalculator = new ScoreCalculator();
    $game = new BowlingGame($scoreCalculator);
    // ゲームオブジェクトをシリアライズしてセッションに保存
    $_SESSION["game"] = serialize($game);
} else {
    // デシリアライズしてゲームオブジェクトに復元
    $game = unserialize($_SESSION["game"]);
}

// スコアボードデータをJSON形式で返す
header("Content-Type: application/json");
echo json_encode([
    "isGameOver" => $game->isGameOver,
    "frames" => $game->getScoreBoard(),
]);
