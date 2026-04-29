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
        <ul className="space-y-1">
          <li>
            <button
              onClick={() => onTagChange("")}
              className={`w-full text-left px-2 py-1 rounded text-sm transition-colors ${
                selectedTag === ""
                  ? "bg-zinc-900 text-white"
                  : "text-zinc-700 hover:bg-zinc-100"
              }`}
            >
              すべて
            </button>
          </li>
          {allTags.map((tag) => (
            <li key={tag}>
              <button
                onClick={() => onTagChange(tag === selectedTag ? "" : tag)}
                className={`w-full text-left px-2 py-1 rounded text-sm transition-colors ${
                  selectedTag === tag
                    ? "bg-zinc-900 text-white"
                    : "text-zinc-700 hover:bg-zinc-100"
                }`}
              >
                {tag}
              </button>
            </li>
          ))}
        </ul>
      </div>
    </aside>
  );
}
