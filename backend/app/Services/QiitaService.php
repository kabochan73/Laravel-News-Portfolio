<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QiitaService
{
    private const BASE_URL = 'https://qiita.com/api/v2';
    private const PER_PAGE = 10;
    private const TAG = 'laravel';

    public function fetchArticles(): array
    {
        $response = Http::get(self::BASE_URL . '/items', [
            'tag' => self::TAG,
            'per_page' => self::PER_PAGE,
        ]);

        $response->throw();

        return collect($response->json())->map(fn($item) => [
            'qiita_id'     => $item['id'],
            'title'        => $item['title'],
            'url'          => $item['url'],
            'author'       => $item['user']['id'],
            'tags'         => collect($item['tags'])->pluck('name')->toArray(),
            'body'         => $item['body'],
            'published_at' => $item['created_at'],
        ])->toArray();
    }
}
