# Rese

## 概要
ある企業のグループ会社の飲食店予約サービス

![トップページ画像](/rese.png)

## 作成した目的
外部の飲食店予約サービスは手数料を取られるので自社で予約サービスを持ちたい。

## 機能一覧

- 会員登録
- ログイン
- ログアウト
- ユーザー情報取得
- ユーザー飲食店お気に入り一覧取得
- ユーザー飲食店予約情報取得
- 飲食店一覧取得
- 飲食店詳細取得
- 飲食店お気に入り追加
- 飲食店お気に入り削除
- 飲食店予約情報追加
- 飲食店予約情報削除
- エリアで検索する
- ジャンルで検索する
- 店名で検索する
- 予約変更機能
- 評価機能
- 店舗代表者作成
- 店舗情報作成
- 店舗情報更新
- 店舗代表者の自店舗に紐づく予約情報取得
- お店の画像をストレージに保存
- メール認証機能
- お知らせメール送信機能
- リマインダー
- 予約のQRコード生成、表示。お店側は照合可能
- 決済機能

## 環境構築

### Dockerビルド
 1. git clone git@github.com:nyanyamarusan/rese.git
 2. docker-compose up -d --build

＊ MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせて docker-compose.yml ファイルを編集してください。

### Laravel環境構築

 1. docker-compose exec php bash
 2. composer install
 3. .env.exampleファイルから.envを作成し、環境変数を変更
 4. php artisan key:generate
 5. php artisan migrate
 6. php artisan db:seed

### .envファイルの設定

- ロケール設定

    - APP_LOCALE=ja
    - APP_FAKER_LOCALE=ja_JP

- セッション、キャッシュ設定

    - SESSION_DRIVER=file
    - CACHE_STORE=file

- ファイルシステム設定

    - FILESYSTEM_DISK=public

- mailhog 環境変数

    - MAIL_MAILER=smtp
    - MAIL_SCHEME=null
    - MAIL_HOST=mailhog
    - MAIL_PORT=1025
    - MAIL_USERNAME=null
    - MAIL_PASSWORD=null
    - MAIL_FROM_ADDRESS="hello@example.com"
    - MAIL_FROM_NAME="${APP_NAME}"

- stripe 環境変数

    - STRIPE_KEY= あなたの公開可能キー
    - STRIPE_SECRET= あなたの秘密キー

＊ もし、変更後に設定が反映されていなかった場合、php artisan config:clear で、キャッシュクリアしてみてください。

## 使用技術

- PHP 8.3-fpm
- Laravel 12.25.0
- MySQL 8.0.41
- nginx 1.26.3
- endroid/qr-code
- mailhog
- stripe
- Fortify
- Bootstrap(CDN経由で読み込み)<br>
    ＊ 自作CSS('public/css/custom.css')も併用

## テーブル設計

![テーブル設計](/rese.drawio.png)

## ER図

![ER図](/rese-er.drawio.png)

## URL

- 開発環境：http://localhost/
- phpMyAdmin：http://localhost:8080/
- Mailhog：http://localhost:8025/
- 一般ユーザー登録：http://localhost/register

## ログイン情報

- 一般ユーザー
    - メールアドレス：user@example.com
    - パスワード：userpass
    - ログイン画面URL：http://localhost/login

- 管理者
    - メールアドレス：admin@example.com
    - パスワード：adminpass
    - ログイン画面URL：http://localhost/admin/login

- 店舗代表者
    - メールアドレス：owner@example.com
    - パスワード：ownerpass
    - ログイン画面URL：http://localhost/owner/login

## stripeについて

- API通信を使用し、セッションを作成、取得する
- テストではMockを使用してAPIへの実通信なして確認可能

## テスト実行方法

1. MySQLにログインし、テーブルを作成する<br>
    テスト用DB名：test
2. PHPコンテナ内で、.envファイルから.env.testingファイルを作成し、環境変数を変更
3. php artisan key:generate --env=testing
4. php artisan config:clear
5. php artisan migrate --env=testing
6. php artisan config:clear
7. vendor/bin/phpunit tests/Feature/実行するテストファイル名

### .env.testingファイルの設定

- APP_ENV=testing
- APP_KEY= ＊php artisan key:generate --env=testingする前は空欄にする
- DB_CONNECTION=mysql_test
- DB_HOST=mysql
- DB_PORT=3306
- DB_DATABASE=test
- DB_USERNAME=root
- DB_PASSWORD=root