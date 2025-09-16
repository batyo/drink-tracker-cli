# Drink Tracker CLI

アルコール摂取量を記録・集計できるシンプルなコマンドラインツールです。

## 特長

- 飲酒記録の追加・一覧表示・集計
- SQLiteデータベースを利用
- 標準ドリンク数や純アルコール量の自動計算

## インストール

1. リポジトリをクローン

```sh
git clone https://github.com/yourname/drink-tracker-cli.git
cd drink-tracker-cli
```

2. 依存パッケージをインストール

```sh
composer install
```

## 使い方

### データベース初期化

```sh
php bin/drink init-db
```

### 飲酒記録の追加

```sh
php bin/drink add <YYYY-MM-DD> <name> <volume_ml> <abv>
```

例:

```sh
php bin/drink add 2025-09-15 Beer 350 5
php bin/drink add 2025-09-15 Wine 150 12
```

- `<YYYY-MM-DD>`: 日付（例: 2025-09-15）
- `<name>`: 飲み物名（例: Beer）
- `<volume_ml>`: 容量（ml単位、例: 350）
- `<abv>`: アルコール度数（%単位、例: 5）

### 記録の一覧表示

```sh
php bin/drink list [from] [to]
```

- `from`, `to` は省略可能。指定しない場合は今月分を表示。

例:

```sh
php bin/drink list 2025-09-01 2025-09-30
```

### 集計表示

```sh
php bin/drink summary [from] [to]
```

- 指定期間内の記録数、総容量、純アルコール量、標準ドリンク数を表示。

例:

```sh
php bin/drink summary 2025-09-01 2025-09-30
```

## テスト

```sh
vendor/bin/phpunit
```

## 設定

- データベースパスや標準ドリンクのグラム数は [config/config.php](config/config.php) で変更できます。

## ライセンス

MIT