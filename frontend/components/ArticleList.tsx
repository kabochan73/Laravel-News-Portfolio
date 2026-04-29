"use client";

import { useState } from "react";
import type { Article } from "@/types/article";
import Sidebar from "@/components/Sidebar";

export default function ArticleList({ articles }: { articles: Article[] }) {
  const [keyword, setKeyword] = useState("");
  const [selectedTag, setSelectedTag] = useState("");

  const allTags = Array.from(new Set(articles.flatMap((a) => a.tags))).sort();

  const filtered = articles.filter((a) => {
    const matchesKeyword =
      keyword === "" ||
      a.title.toLowerCase().includes(keyword.toLowerCase()) ||
      a.summary.toLowerCase().includes(keyword.toLowerCase());

    const matchesTag = selectedTag === "" || a.tags.includes(selectedTag);

    return matchesKeyword && matchesTag;
  });

  return (
    <div className="flex gap-6 p-6 max-w-6xl mx-auto w-full">
      {/* Article cards (3/4) */}
      <main className="flex-3">
        {/* <p className="text-sm text-zinc-500 mb-4">{filtered.length} 件</p> */}
        {filtered.length === 0 ? (
          <p className="text-zinc-400">記事が見つかりませんでした。</p>
        ) : (
          <ul className="space-y-4">
            {filtered.map((article) => (
              <li
                key={article.id}
                className="rounded-lg border border-zinc-200 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md hover:-translate-y-1"
              >
                <a
                  href={article.url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-lg font-semibold text-zinc-900 hover:underline"
                >
                  {article.title}
                </a>
                <p className="mt-2 text-sm text-zinc-600 leading-relaxed">
                  {article.summary}
                </p>
                <div className="mt-3 flex flex-wrap items-center gap-2">
                  {article.tags.map((tag) => (
                    <span
                      key={tag}
                      className="rounded-full bg-zinc-100 px-2 py-0.5 text-xs text-zinc-600"
                    >
                      {tag}
                    </span>
                  ))}
                  <span className="ml-auto text-xs text-zinc-400">
                    {article.author} ·{" "}
                    {new Date(article.published_at).toLocaleDateString("ja-JP")}
                  </span>
                </div>
              </li>
            ))}
          </ul>
        )}
      </main>

      {/* Sidebar (1/4) */}
      <div className="flex-1">
        <Sidebar
          allTags={allTags}
          keyword={keyword}
          selectedTag={selectedTag}
          onKeywordChange={setKeyword}
          onTagChange={setSelectedTag}
        />
      </div>
    </div>
  );
}
