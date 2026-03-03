# 勤怠管理アプリ
[機能要件](https://docs.google.com/spreadsheets/d/17p-jmsXQr_Es3-n9rn6ox_B9ifDG317RwXj7JBGVmvs/edit?gid=1909938334#gid=1909938334) + 以下の応用要件を実装しています

&nbsp;

## 環境構築

#### 1. リポジトリをクローン

```bash
git clone git@github.com:neuro-chan/COACHTECH-Flea-Market.git
cd coachtech-flea-market
```

#### 2. Docker Desktop を起動

`make init` 実行前にDocker Desktop を起動してください。

#### 3. 初期セットアップ

```bash
make init #プロジェクトルートで実行
```

`make init` では以下の処理が自動で実行されます

- Dockerイメージのビルド
- コンテナ起動
- .env（.env.example → .env）の配置
- Composerインストール
- アプリケーションキー生成
- DBのマイグレーション・初期データのシーディング
- storage / bootstrap/cache の書き込み権限調整
- ストレージのシンボリックリンク作成

&nbsp;
#### トラブルシューティング
 `storage/logs` や `bootstrap/cache` への書き込み権限が不足し、`Permission denied` が発生する場合は以下を実行してください。

```bash
docker compose exec -T php chown -R www-data:www-data storage bootstrap/cache || true
docker compose exec -T php chmod -R 775 storage bootstrap/cache
```
`make init` 実行時に `Access denied` エラーが発生した場合は、以下のコマンドでボリュームを削除してから再実行してください。

```bash
docker compose down -v
make init
```

&nbsp;

## MailHogの設定

必要な設定は `.env.example` に含まれているため、追加の設定は不要です。
Featureテストでは `Notification::fake()` を使用しているため、テスト実行時にMailHogは不要です。

&nbsp;

## 使用技術

- バックエンド：Laravel 12 / PHP 8.3
- フロントエンド：HTML/ CSS/ JavaScript
- データベース：MySQL 8.0
- 開発環境：Docker / Nginx / phpMyAdmin
- バージョン管理：Git / GitHub
- メール（開発環境）：MailHog
- テスト：PHPUnit（Featureテスト）

&nbsp;

## 基本設計書
https://docs.google.com/spreadsheets/d/17p-jmsXQr_Es3-n9rn6ox_B9ifDG317RwXj7JBGVmvs/edit?gid=574125123#gid=574125123

## テーブル仕様書
https://docs.google.com/spreadsheets/d/17p-jmsXQr_Es3-n9rn6ox_B9ifDG317RwXj7JBGVmvs/edit?gid=1188247583#gid=1188247583

&nbsp;

## ER図

![ER図](src/er.draw.png)

&nbsp;
