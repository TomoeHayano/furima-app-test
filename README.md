# furima-app-test　環境構築


## Dokerビルド

- git clone https://github.com/TomoeHayano/furima-app-test.git

- docker-compose up -d --build

## laravel環境構築

- docker-compose exec php bash

- composer install

- cp .env.example .env

- php artisan key:generate

- php artisan migrate

- php artisan db:seed

## 開発環境
- 商品一覧画面(トップ画面):
- 会員登録:http://
- ログイン画面:http://login
- phpMyAdmin:http://localhost:8080

## 使用技術（実行環境）
- nginx:1.21.1
- mysql:8.0.26
- docker:3.8
- php:8.1

