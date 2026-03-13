import "./globals.css";
import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Lian-Heart_web",
  description: "Lian-Heart_web の専用ウェブサイトを構築するためのリポジトリです。",
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
