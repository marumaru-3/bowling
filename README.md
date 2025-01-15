## ボウリングゲーム

#### 遊び方

左下の「ボールを投げる」ボタンを押すとゲームを進めることができる。
右下の「最初から始める」ボタンを押すとゲームデータがリセットされる。

## プロジェクトの概要

#### プロジェクトの目的

簡単なボウリングゲームの Web アプリを作成して、PHP とサーバーサイドの理解を深める

#### プロジェクト行程

- 開発開始日:2025/1/4
- リリース:~2025/1/15

## 主要技術

| 言語   | バージョン |
| ------ | ---------- |
| php    | 8.3.1      |
| Apache |            |

## ディレクトリ構成

```
.
│  .htaccess
│  index.php
│  README.md
│
├─assets
│  ├─css
│  │      reset.css
│  │      style.css
│  │
│  ├─images
│  │      bowling-bg.webp
│  │      bowling_ball.webp
│  │      bowling_strike.webp
│  │
│  └─js
│          main.js
│
└─src
    ├─controllers
    │      getGameData.php
    │      resetGame.php
    │      throwBall.php
    │
    └─models
            BowlingGame.php
```
