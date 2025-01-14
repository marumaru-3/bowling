<?php
// セッションの開始
session_start();

// BowlingGameクラスファイルの読み込み
require_once "../models/BowlingGame.php";

// セッションからゲームの状態を取得（初回リクエストなら新規作成）
if (!isset($_SESSION["game"])) {
    $scoreCalculator = new ScoreCalculator();
    $game = new BowlingGame($scoreCalculator);
    // ゲームオブジェクトをシリアライズしてセッションに保存
    // シリアライズ：配列をそのまま配列としてサーバーに保存するために必要
    $_SESSION["game"] = serialize($game);
} else {
    // デシリアライズしてゲームオブジェクトに復元
    $game = unserialize($_SESSION["game"]);
}

// ボタンがクリックされたら1投
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$game->isGameOver) {
    $game->throwBall();
    // 更新した状態をセッションに保存
    $_SESSION["game"] = serialize($game);
}

// スコアボードデータをJSON形式で返す
header("Content-Type: application/json");
echo json_encode([
    "isGameOver" => $game->isGameOver,
    "frames" => $game->getScoreBoard(),
]);
