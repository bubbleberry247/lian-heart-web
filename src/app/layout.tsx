import type { Metadata } from "next";
import { lpContent } from "./content";
import "./globals.css";

const siteUrl = process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/$/, "");
const shouldNoindex = process.env.NEXT_PUBLIC_NOINDEX === "true" || !siteUrl;
const ogImage = "/images/hero-care.jpg";

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl ?? "http://localhost:3000"),
  title: lpContent.metadata.title,
  description: lpContent.metadata.description,
  applicationName: lpContent.brand,
  alternates: siteUrl ? { canonical: "/" } : undefined,
  robots: shouldNoindex
    ? { index: false, follow: false }
    : {
        index: true,
        follow: true,
        googleBot: {
          index: true,
          follow: true,
          "max-snippet": -1,
          "max-image-preview": "large",
          "max-video-preview": -1,
        },
      },
  openGraph: {
    title: lpContent.metadata.title,
    description: lpContent.metadata.description,
    siteName: lpContent.brand,
    url: siteUrl || undefined,
    locale: "ja_JP",
    type: "website",
    images: [
      {
        url: ogImage,
        width: 1200,
        height: 630,
        alt: `${lpContent.brand}の老人ホーム紹介・入居相談`,
      },
    ],
  },
  twitter: {
    card: "summary_large_image",
    title: lpContent.metadata.title,
    description: lpContent.metadata.description,
    images: [ogImage],
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ja">
      <body>{children}</body>
    </html>
  );
}
