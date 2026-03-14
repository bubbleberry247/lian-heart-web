import { NextRequest, NextResponse } from "next/server";

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
    const formData = await request.formData();

    // Honeypot
    if (hasValue(getValue(formData, "hp"))) {
      return redirectWithStatus(request, "success");
    }

    const payload: ContactPayload = {
      timestamp: new Date().toISOString(),
      name: getValue(formData, "name"),
      furigana: getValue(formData, "furigana"),
      phone: getValue(formData, "phone"),
      email: getValue(formData, "email"),
      relationship: getValue(formData, "relationship"),
      area: getValue(formData, "area"),
      facilityType: getValue(formData, "facilityType"),
      careLevel: getValue(formData, "careLevel"),
      budget: getValue(formData, "budget"),
      moveInDate: getValue(formData, "moveInDate"),
      message: getValue(formData, "message"),
      privacy: getValue(formData, "privacy"),
      sourceUrl: request.headers.get("referer") || request.url,
      userAgent: request.headers.get("user-agent") || "",
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

