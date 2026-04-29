<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'qiita_id'     => $this->qiita_id,
            'title'        => $this->title,
            'url'          => $this->url,
            'author'       => $this->author,
            'tags'         => $this->tags,
            'summary'      => $this->summary,
            'published_at' => $this->published_at->toIso8601String(),
        ];
    }
}
