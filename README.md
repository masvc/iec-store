# IEC Store（EC サイトプロトタイプ）

## アプリケーション概要

シンプルな EC サイトのプロトタイプです。商品の出品、購入、カート管理、ユーザー管理などの基本的な機能を実装しています。

## 使用技術

- PHP 8.2
- MySQL 8.0
- Apache 2.4
- Tailwind CSS

## 主な機能

### 商品管理機能

- 商品の出品（画像アップロード対応）
- 商品の編集・削除
- カテゴリー別表示
- 価格順での並び替え

### ショッピング機能

- カートへの追加
- カート内商品の数量変更
- 注文処理
- 配送先情報の管理

### ユーザー管理機能

- アカウント作成
- ログイン/ログアウト
- マイページでの出品商品管理

## 工夫した点

- シンプルで使いやすい UI の実現
- 画像アップロード機能の実装
- カテゴリー別表示や価格順での並び替えなど、ユーザビリティの向上

## 今後の課題・追加したい機能

- 商品レビュー機能
- お気に入り機能
- 決済機能の実装
- 検索機能の強化

## ディレクトリ構成

iec/
├── README.md # プロジェクトの説明書
├── includes/ # 共通ファイル
│ ├── config.php # 設定ファイル
│ ├── functions.php # 共通関数
│ ├── db_connect.php # データベース接続
│ ├── header.php # 共通ヘッダー
│ ├── footer.php # 共通フッター
│ └── head.php # HTML head 部分
├── data/ # データ関連
├── uploads/ # 商品画像アップロード先
└── その他 PHP ファイル群