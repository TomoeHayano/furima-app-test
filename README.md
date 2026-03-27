# furima-app-test　環境構築


## Dokerビルド

- git clone git@github.com:TomoeHayano/furima-app-test.git
- docker-compose up -d --build

## laravel環境構築

- docker-compose exec php bash

- composer install

- cp .env.example .env

- php artisan key:generate

- php artisan migrate

- php artisan db:seed

- php artisan storage:link

## 開発環境
- 商品一覧画面(トップ画面):http://localhost/
- 会員登録:http://localhost/register
- ログイン画面:http://login
- phpMyAdmin:http://localhost:8080
- mailHog:http://localhost:8025

## 使用技術（実行環境）
- nginx:1.21.1
- mysql:8.0.26
- docker:3.8
- php:8.1

## Stripe実行方法（支払い方法選択）
- テスト用クレジットカード：4242 4242 4242 4242（有効期限・CVC は任意の未来日と 3 桁）

## 環境変数
- .envとenv.testingに<br>
STRIPE_KEY・STRIPE_SECRET　は未設定のため、KEYの取得をお願いいたします。

## Unitテスト実行方法
- このプロジェクトでは、LaravelのFeatureテストを一部実装しています。<br>
テスト実行環境はDockerコンテナ内で完結します。

### 初期設定
- docker-compose exec php bash
- mysql -u root -p<br>
※ パスワード: root
- CREATE DATABASE demo_test;
- exit
- docker-compose exec php bash
- php artisan key:generate
- php artisan migrate:fresh --env=testing

### 実行手順
- docker-compose exec php bash
- php artisan test tests/Feature/＊各ファイル名＊

### ユーザー情報
- name：Seller One<br>
email：seller1@example.com<br>
password：111111111<br>
出品商品：CO01 腕時計<br>
CO02 HDD<br>
CO03　玉ねぎ3束<br>
CO04　革靴<br>
CO05　ノートPC<br>
       
- name：Seller Two<br>
email：seller2@example.com<br>
passwor：111111111<br>
出品商品：CO06　マイク<br>
CO07　ショルダーバッグ<br>
CO08　タンブラー<br>
CO09　コーヒーミル<br>
CO10　メイクセット<br>

- name：Unassigned User<br>
email：user3@example.com<br>
password：111111111

## ER図
![ER図](20260102_er_diagram.png)


## テーブル
### usersテーブル（ユーザー）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| name | varchar(20) |  |  | ○ |  |
| email | varchar(255) |  | ○ | ○ |  |
| email_verified_at | timestamp |  |  |  |  |
| password | varchar(255) |  |  | ○ |  |
| remember_token | varchar |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### profilesテーブル（プロフィール）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| user_id | unsigned bigint |  |  | ○ | users(id) |
| postal_code | varchar(8) |  |  | ○ |  |
| address | varchar(255) |  |  | ○ |  |
| building_name | varchar(255) |  |  |  |  |
| image_path | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### productsテーブル（商品）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| user_id | unsigned bigint |  |  | ○ | users(id) |
| name | varchar(255) |  |  | ○ |  |
| brand_name | varchar(255) |  |  |  |  |
| description | text |  |  | ○ |  |
| price | integer |  |  | ○ |  |
| condition_id | integer |  |  | ○ | product_conditions(id) |
| image_path | varchar(255) |  |  | ○ |  |
| is_sold | tinyint |  |  | ○ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### categoriesテーブル（カテゴリ）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| name | varchar(255) |  | ○ | ○ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### product_categoryテーブル（商品カテゴリ中間）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| product_id | unsigned bigint |  |  | ○ | products(id) |
| category_id | unsigned bigint |  |  | ○ | categories(id) |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### likesテーブル（いいね）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| user_id | unsigned bigint |  |  | ○ | users(id) |
| product_id | unsigned bigint |  |  | ○ | products(id) |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### commentsテーブル（コメント）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| user_id | unsigned bigint |  |  | ○ | users(id) |
| product_id | unsigned bigint |  |  | ○ | products(id) |
| content | varchar(255) |  |  | ○ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### ordersテーブル（注文）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| user_id | unsigned bigint |  |  | ○ | users(id) |
| product_id | unsigned bigint |  |  | ○ | products(id) |
| profile_id | unsigned bigint |  |  | ○ | profiles(id) |
| payment_method | varchar(50) |  |  | ○ |  |
| shipping_postal_code | varchar(8) |  |  | ○ |  |
| shipping_address | varchar(255) |  |  | ○ |  |
| shipping_building_name | varchar(255) |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### product_conditionsテーブル（商品状態）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| status_name | varchar(255) |  | ○ | ○ |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### transactionsテーブル（取引）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| order_id | unsigned bigint |  | ○ | ○ | orders(id) |
| buyer_id | unsigned bigint |  |  | ○ | users(id) |
| seller_id | unsigned bigint |  |  | ○ | users(id) |
| buyer_rating | unsigned tinyint |  |  |  |  |
| seller_rating | unsigned tinyint |  |  |  |  |
| buyer_rated_at | timestamp |  |  |  |  |
| seller_rated_at | timestamp |  |  |  |  |
| buyer_completed_at | timestamp |  |  |  |  |
| seller_completed_at | timestamp |  |  |  |  |
| completed_at | timestamp |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |

### transaction_messagesテーブル（取引メッセージ）
| カラム名 | 型 | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY |
| --- | --- | --- | --- | --- | --- |
| id | unsigned bigint | ○ |  | ○ |  |
| transaction_id | unsigned bigint |  |  | ○ | transactions(id) |
| sender_id | unsigned bigint |  |  | ○ | users(id) |
| body | varchar(1000) |  |  | ○ |  |
| image_path | varchar(255) |  |  |  |  |
| read_at | timestamp |  |  |  |  |
| created_at | timestamp |  |  |  |  |
| updated_at | timestamp |  |  |  |  |
