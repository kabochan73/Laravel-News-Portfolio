<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Services\ClaudeService;
use App\Services\QiitaService;
use Illuminate\Console\Command;

class FetchArticles extends Command
{
    protected $signature = 'articles:fetch';

    protected $description = 'Fetch Laravel articles from Qiita, summarize new ones with Claude, and save to DB';

    private const MAX_ARTICLES = 100;

    public function __construct(
        private readonly QiitaService $qiitaService,
        private readonly ClaudeService $claudeService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $fetched = $this->qiitaService->fetchArticles();

        $fetchedIds = array_column($fetched, 'qiita_id');
        $existingIds = Article::whereIn('qiita_id', $fetchedIds)->pluck('qiita_id')->toArray();

        $newArticles = array_filter($fetched, fn($a) => !in_array($a['qiita_id'], $existingIds));

        if (empty($newArticles)) {
            $this->info('No new articles found.');
            return self::SUCCESS;
        }

        foreach ($newArticles as $article) {
            $article['summary'] = $this->claudeService->summarize($article['body']);
            Article::create($article);
            $this->info("Saved: {$article['title']}");
        }

        $this->pruneOldArticles();

        return self::SUCCESS;
    }

    private function pruneOldArticles(): void
    {
        $count = Article::count();

        if ($count <= self::MAX_ARTICLES) {
            return;
        }

        $deleteCount = $count - self::MAX_ARTICLES;

        Article::orderBy('published_at')
            ->limit($deleteCount)
            ->delete();

        $this->info("Pruned {$deleteCount} old article(s).");
    }
}
