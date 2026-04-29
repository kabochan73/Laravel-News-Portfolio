<?php

namespace App\Services;

use Anthropic\Client;

class ClaudeService
{
    private const SYSTEM_PROMPT = '
あなたはQiitaの技術記事を要約するアシスタントです。
与えられたMarkdown形式の記事本文を、以下のルールで要約してください。

- 日本語で3〜5文程度にまとめる
- 記事の主要なトピック・技術・手順を簡潔に伝える
- コードブロックや細かい実装詳細は省略する
- 読者が記事の価値を判断できる内容にする
';

    private Client $client;

    public function __construct()
    {
        $this->client = new Client(apiKey: env('ANTHROPIC_API_KEY'));
    }

    public function summarize(string $body): string
    {
        $message = $this->client->messages->create(
            model: 'claude-opus-4-7',
            maxTokens: 1024,
            system: [
                [
                    'type'         => 'text',
                    'text'         => self::SYSTEM_PROMPT,
                    'cacheControl' => ['type' => 'ephemeral'],
                ],
            ],
            messages: [
                ['role' => 'user', 'content' => $body],
            ],
        );

        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                return $block->text;
            }
        }

        return '';
    }
}
