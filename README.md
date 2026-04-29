# Qiita Laravel News

QiitaのLaravel記事を自動収集・AI要約して表示するビューアーアプリです。
2日おきにQiita APIからLaravel記事を取得し、Claude APIで日本語要約を生成してDBに保存します。フロントエンドではキーワード検索・タグフィルタリングで記事を絞り込めます。

## 作成理由など

少し前にGuzzleを使って、すごく感動したのでポートフォリオに何かスクレイピングのアプリを入れようと思い作りました。
正確にはAPIを使ってるので、スクレイピングではないですが。AI要約機能は、設計してる時に相性がいいと思い実装してみました。思いの外簡単ですごく便利で入れて良かったと思っています。  
ログイン機能は実装しても大した恩恵がないと判断して省きました。その結果DBもファイルもスッキリしたので、とても満足しています。

## デプロイ先

| サービス         | URL                                                                           |
| ---------------- | ----------------------------------------------------------------------------- |
| バックエンド API | https://laravel-news-portfoloi-backend-main-lcocgq.laravel.cloud/api/articles |
| フロントエンド   | https://laravel-news-portfolio-frontend.vercel.app                            |

## 技術スタック

| レイヤー       | 技術                                                |
| -------------- | --------------------------------------------------- |
| フロントエンド | Next.js 16 / TypeScript / Tailwind CSS / Node.js 25 |
| バックエンド   | Laravel 13 / PHP 8.5                                |
| AI             | Claude API（Anthropic）                             |
| DB             | PostgreSQL 18                                       |
| インフラ       | Docker / Nginx / Laravel Cloud / Vercel             |

## 機能

- **自動記事収集** — Laravelスケジューラで2日おきにQiita APIから20件取得
- **AI要約** — Claude APIで記事本文を日本語3〜5文に要約（プロンプトキャッシュで省コスト化）
- **重複排除** — 取得済み記事はClaudeを呼ばずスキップ
- **上限管理** — 最大100件を保持し、古い記事から自動削除
- **キーワード検索** — タイトル・要約をフロントエンドで絞り込み
- **タグフィルタリング** — 記事に付いたタグで絞り込み
- **ISR** — 2日キャッシュで高速表示

## セットアップ

### 1. リポジトリをクローン

```bash
git clone <repo-url>
cd Laravel-News-Portfolio
```

### 2. 環境変数を設定

```bash
cp backend/.env.example backend/.env
```

`backend/.env` を開いて `ANTHROPIC_API_KEY` を設定します。

```
ANTHROPIC_API_KEY=sk-ant-xxxxxxxxxxxxxxxxxx
```

### 3. 起動

```bash
docker compose up --build
```

| サービス         | URL                                |
| ---------------- | ---------------------------------- |
| フロントエンド   | http://localhost:3000              |
| バックエンド API | http://localhost:8080/api/articles |

### 4. 記事を取得する

初回は手動でコマンドを実行してください。

```bash
docker compose exec app php artisan articles:fetch
```

以降は2日おきに自動実行されます。

## アーキテクチャ

```
[cron] → articles:fetch (2日おき)
    ├─ QiitaService  → Qiita API（20件取得）
    ├─ ClaudeService → Claude API（新規記事のみ要約）
    └─ Article::create() → PostgreSQL

[Next.js page.tsx] → GET /api/articles → PostgreSQL
    └─ ArticleList.tsx（クライアント）
          ├─ キーワード検索
          ├─ タグフィルタリング
          └─ Sidebar.tsx
```

## テスト

```bash
docker compose exec app php artisan test
```
