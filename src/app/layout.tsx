import type { Metadata } from "next";
import { lpContent } from "./content";
import "./globals.css";

export const metadata: Metadata = {
  title: lpContent.metadata.title,
  description: lpContent.metadata.description,
  applicationName: lpContent.brand,
  robots: { index: false, follow: false },
  openGraph: {
    title: lpContent.metadata.title,
    description: lpContent.metadata.description,
    siteName: lpContent.brand,
    locale: "ja_JP",
    type: "website",
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
