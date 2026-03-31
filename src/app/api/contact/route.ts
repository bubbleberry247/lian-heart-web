import { NextRequest, NextResponse } from "next/server";

// ---------------------------------------------------------------------------
// A. Origin検証（軽量CSRF対策）
// ---------------------------------------------------------------------------

function isOriginAllowed(request: NextRequest): boolean {
  const allowedOrigin = process.env.NEXT_PUBLIC_SITE_URL;
  // 環境変数未設定の場合はスキップ（開発環境対応）
  if (!allowedOrigin) return true;

  const requestOrigin = request.headers.get("origin");
  if (!requestOrigin) return false;

  // トレイリングスラッシュを除去して比較
  const normalize = (url: string) => url.replace(/\/+$/, "");
  return normalize(requestOrigin) === normalize(allowedOrigin);
}

// ---------------------------------------------------------------------------
// B. 簡易レート制限（IPベース / Map + タイムスタンプ方式）
// ---------------------------------------------------------------------------
// TODO: 本格運用時は Upstash Redis 等の永続ストアに移行すべき。
// Vercel のサーバレス環境ではインスタンスが再生成されるたびに Map がリセット
// されるため、この方式は完全な防御にはならない。ゼロ防御よりはマシという位置づけ。

const RATE_LIMIT_WINDOW_MS = 60_000; // 60秒
const RATE_LIMIT_MAX_REQUESTS = 3;

const rateLimitMap = new Map<string, number[]>();

function getClientIp(request: NextRequest): string {
  return (
    request.headers.get("x-forwarded-for")?.split(",")[0]?.trim() ||
    request.headers.get("x-real-ip") ||
    "unknown"
  );
}

function isRateLimited(ip: string): boolean {
  const now = Date.now();
  const timestamps = rateLimitMap.get(ip) ?? [];

  // ウィンドウ外の古いタイムスタンプを除去
  const recent = timestamps.filter((t) => now - t < RATE_LIMIT_WINDOW_MS);

  if (recent.length >= RATE_LIMIT_MAX_REQUESTS) {
    // 更新しておく（古いエントリのGC）
    rateLimitMap.set(ip, recent);
    return true;
  }

  recent.push(now);
  rateLimitMap.set(ip, recent);
  return false;
}

// ---------------------------------------------------------------------------
// C. スプレッドシート式インジェクション対策
// ---------------------------------------------------------------------------
// Google Sheets は先頭が =, +, -, @ の文字列を数式として解釈するため、
// シングルクォートを前置してテキストリテラルとして扱わせる。

function sanitizeForSpreadsheet(value: string): string {
  if (/^[=+\-@\t\r\n]/.test(value)) {
    return "'" + value;
  }
  return value;
}

// ---------------------------------------------------------------------------
// D. フィールド文字数上限
// ---------------------------------------------------------------------------

const FIELD_MAX_LENGTHS: Record<string, number> = {
  name: 100,
  furigana: 100,
  phone: 20,
  email: 254,
  relationship: 100,
  area: 100,
  facilityType: 100,
  careLevel: 100,
  budget: 100,
  moveInDate: 100,
  message: 2000,
};

// ---------------------------------------------------------------------------
// Types
// ---------------------------------------------------------------------------

type ContactPayload = {
  timestamp: string;
  name: string;
  furigana: string;
  phone: string;
  email: string;
  relationship: string;
  area: string;
  facilityType: string;
  careLevel: string;
  budget: string;
  moveInDate: string;
  message: string;
  privacy: string;
  sourceUrl: string;
  userAgent: string;
  hp?: string;
};

type GasResponsePayload = {
  ok?: boolean;
};

function getValue(formData: FormData, key: string): string {
  const raw = formData.get(key);
  if (typeof raw !== "string") return "";
  return raw.trim();
}

function hasValue(value: string): boolean {
  return value.length > 0;
}

function redirectWithStatus(request: NextRequest, contact: "success" | "error") {
  const url = new URL("/", request.url);
  url.searchParams.set("contact", contact);
  url.hash = "contact";
  return NextResponse.redirect(url, 303);
}

export async function POST(request: NextRequest) {
  try {
    // A. Origin検証
    if (!isOriginAllowed(request)) {
      return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    }

    // B. レート制限
    const clientIp = getClientIp(request);
    if (isRateLimited(clientIp)) {
      return NextResponse.json({ error: "Too Many Requests" }, { status: 429 });
    }

    const formData = await request.formData();

    // Honeypot
    if (hasValue(getValue(formData, "hp"))) {
      return redirectWithStatus(request, "success");
    }

    // D. フィールド文字数上限チェック
    for (const [field, maxLen] of Object.entries(FIELD_MAX_LENGTHS)) {
      if (getValue(formData, field).length > maxLen) {
        return redirectWithStatus(request, "error");
      }
    }

    const payload: ContactPayload = {
      timestamp: sanitizeForSpreadsheet(new Date().toISOString()),
      name: sanitizeForSpreadsheet(getValue(formData, "name")),
      furigana: sanitizeForSpreadsheet(getValue(formData, "furigana")),
      phone: sanitizeForSpreadsheet(getValue(formData, "phone")),
      email: sanitizeForSpreadsheet(getValue(formData, "email")),
      relationship: sanitizeForSpreadsheet(getValue(formData, "relationship")),
      area: sanitizeForSpreadsheet(getValue(formData, "area")),
      facilityType: sanitizeForSpreadsheet(getValue(formData, "facilityType")),
      careLevel: sanitizeForSpreadsheet(getValue(formData, "careLevel")),
      budget: sanitizeForSpreadsheet(getValue(formData, "budget")),
      moveInDate: sanitizeForSpreadsheet(getValue(formData, "moveInDate")),
      message: sanitizeForSpreadsheet(getValue(formData, "message")),
      privacy: getValue(formData, "privacy"),
      sourceUrl: sanitizeForSpreadsheet(request.headers.get("referer") || request.url),
      userAgent: sanitizeForSpreadsheet(request.headers.get("user-agent") || ""),
      hp: getValue(formData, "hp"),
    };

    if (!payload.name || !payload.furigana || !payload.phone || !payload.message || !payload.privacy) {
      return redirectWithStatus(request, "error");
    }

    const gasUrl = process.env.GAS_WEB_APP_URL;
    if (!gasUrl) {
      return redirectWithStatus(request, "error");
    }

    const gasResponse = await fetch(gasUrl, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload),
      cache: "no-store",
    });

    if (!gasResponse.ok) {
      return redirectWithStatus(request, "error");
    }

    const gasResult = (await gasResponse.json().catch(() => null)) as GasResponsePayload | null;

    if (!gasResult?.ok) {
      return redirectWithStatus(request, "error");
    }

    return redirectWithStatus(request, "success");
  } catch {
    return redirectWithStatus(request, "error");
  }
}

