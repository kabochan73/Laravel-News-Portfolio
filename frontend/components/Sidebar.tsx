"use client";

type Props = {
  allTags: string[];
  keyword: string;
  selectedTag: string;
  onKeywordChange: (value: string) => void;
  onTagChange: (tag: string) => void;
};

export default function Sidebar({
  allTags,
  keyword,
  selectedTag,
  onKeywordChange,
  onTagChange,
}: Props) {
  return (
    <aside className="space-y-6">
      <div>
        <h2 className="text-sm font-semibold text-zinc-500 uppercase tracking-wide mb-2">
          キーワード
        </h2>
        <input
          type="text"
          placeholder="記事を検索..."
          value={keyword}
          onChange={(e) => onKeywordChange(e.target.value)}
          className="w-full rounded-md border border-zinc-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-zinc-400"
        />
      </div>

      <div>
        <h2 className="text-sm font-semibold text-zinc-500 uppercase tracking-wide mb-2">
          タグ
        </h2>
        <div className="flex flex-wrap gap-2">
          <button
            onClick={() => onTagChange("")}
            className={`rounded-full px-3 py-1 text-xs transition-colors ${
              selectedTag === ""
                ? "bg-zinc-900 text-white"
                : "bg-zinc-100 text-zinc-600 hover:bg-zinc-200 hover:-translate-y-0.5"
            }`}
          >
            すべて
          </button>
          {allTags.map((tag) => (
            <button
              key={tag}
              onClick={() => onTagChange(tag === selectedTag ? "" : tag)}
              className={`rounded-full px-3 py-1 text-xs transition-colors ${
                selectedTag === tag
                  ? "bg-zinc-900 text-white"
                  : "bg-zinc-100 text-zinc-600 hover:bg-zinc-300 hover:-translate-y-0.5"
              }`}
            >
              {tag}
            </button>
          ))}
        </div>

      </div>
    </aside>
  );
}
