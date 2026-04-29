import ArticleList from "@/components/ArticleList";
import type { Article } from "@/types/article";

export const revalidate = 172800; // 2日

async function fetchArticles(): Promise<Article[]> {
  const res = await fetch("http://localhost/api/articles");
  if (!res.ok) throw new Error("Failed to fetch articles");
  const json = await res.json();
  return json.data;
}

export default async function Home() {
  const articles = await fetchArticles();

  return <ArticleList articles={articles} />;
}
