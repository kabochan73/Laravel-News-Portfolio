<?php

namespace Tests\Feature;

use App\Console\Commands\FetchArticles;
use App\Models\Article;
use App\Services\ClaudeService;
use App\Services\QiitaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchArticlesTest extends TestCase
{
    use RefreshDatabase;

    private function makeArticleData(string $qiitaId, string $publishedAt = '2024-01-01T00:00:00+09:00'): array
    {
        return [
            'qiita_id'     => $qiitaId,
            'title'        => "Title {$qiitaId}",
            'url'          => "https://qiita.com/items/{$qiitaId}",
            'author'       => 'author',
            'tags'         => ['Laravel'],
            'body'         => 'body text',
            'published_at' => $publishedAt,
        ];
    }

    public function test_新規記事が保存される(): void
    {
        $qiitaService = $this->mock(QiitaService::class);
        $claudeService = $this->mock(ClaudeService::class);

        $qiitaService->shouldReceive('fetchArticles')
            ->once()
            ->andReturn([$this->makeArticleData('abc123')]);

        $claudeService->shouldReceive('summarize')
            ->once()
            ->andReturn('要約テキスト');

        $this->artisan('articles:fetch')->assertSuccessful();

        $this->assertDatabaseHas('articles', [
            'qiita_id' => 'abc123',
            'summary'  => '要約テキスト',
        ]);
    }

    public function test_既存記事はスキップされClaudeを呼ばない(): void
    {
        Article::create($this->makeArticleData('existing') + ['summary' => '既存の要約']);

        $qiitaService = $this->mock(QiitaService::class);
        $claudeService = $this->mock(ClaudeService::class);

        $qiitaService->shouldReceive('fetchArticles')
            ->once()
            ->andReturn([$this->makeArticleData('existing')]);

        $claudeService->shouldReceive('summarize')->never();

        $this->artisan('articles:fetch')->assertSuccessful();

        $this->assertDatabaseCount('articles', 1);
    }

    public function test_新規記事がない場合は何も保存されない(): void
    {
        $qiitaService = $this->mock(QiitaService::class);
        $claudeService = $this->mock(ClaudeService::class);

        $qiitaService->shouldReceive('fetchArticles')
            ->once()
            ->andReturn([]);

        $claudeService->shouldReceive('summarize')->never();

        $this->artisan('articles:fetch')->assertSuccessful();

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_100件超えたら古い記事から削除される(): void
    {
        // 100件作成（published_atを古い順に設定）
        for ($i = 1; $i <= 100; $i++) {
            $date = now()->subDays(200 - $i)->toIso8601String();
            Article::create($this->makeArticleData("old_{$i}", $date) + ['summary' => '要約']);
        }

        $qiitaService = $this->mock(QiitaService::class);
        $claudeService = $this->mock(ClaudeService::class);

        $qiitaService->shouldReceive('fetchArticles')
            ->once()
            ->andReturn([$this->makeArticleData('new_article', now()->toIso8601String())]);

        $claudeService->shouldReceive('summarize')->once()->andReturn('新しい要約');

        $this->artisan('articles:fetch')->assertSuccessful();

        $this->assertDatabaseCount('articles', 100);
        $this->assertDatabaseMissing('articles', ['qiita_id' => 'old_1']);
        $this->assertDatabaseHas('articles', ['qiita_id' => 'new_article']);
    }
}
