#!/bin/sh
set -e

# Next.jsプロジェクトが未初期化の場合は作成する
if [ ! -f /app/package.json ]; then
  echo "Next.js project not found. Creating..."
  npx create-next-app@latest . \
    --typescript \
    --tailwind \
    --eslint \
    --app \
    --no-src-dir \
    --import-alias "@/*" \
    --yes
fi

if [ ! -d /app/node_modules ]; then
  npm install
fi

exec npm run dev
