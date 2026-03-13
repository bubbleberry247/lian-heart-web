import styles from "./page.module.css";

const sections = [
  {
    title: "Message",
    body: "最初に決めるべきは、誰に何を伝えるサイトかです。トップページには1つの主張だけを置くと設計がぶれません。",
  },
  {
    title: "Structure",
    body: "LPなら1ページ集中、コーポレートなら情報設計優先、サービスサイトなら導線優先です。最初に型を決めると実装が速くなります。",
  },
  {
    title: "Launch",
    body: "このリポジトリは独立運用前提です。デザイン、文言、デプロイ先をここで完結させられます。",
  },
];

const checklist = [
  "サイト名とドメイン候補を決める",
  "必要ページを洗い出す",
  "ブランドカラーとフォントを確定する",
  "公開先を Vercel / GitHub Pages / さくら等から選ぶ",
];

export default function Home() {
  return (
    <main className={styles.page}>
      <section className={styles.hero}>
        <p className={styles.eyebrow}>Lian-Heart_web</p>
        <h1>新しいウェブサイトを、既存案件と切り離して育てる。</h1>
        <p className={styles.lead}>
          このリポジトリは新規サイト専用の土台です。ページ設計、ブランド調整、公開作業をここで独立して進められます。
        </p>

        <div className={styles.actions}>
          <a
            className={styles.primaryAction}
            href="https://nextjs.org/docs"
            target="_blank"
            rel="noreferrer"
          >
            Next.js Docs
          </a>
          <a
            className={styles.secondaryAction}
            href="https://vercel.com/new"
            target="_blank"
            rel="noreferrer"
          >
            Deploy Options
          </a>
        </div>
      </section>

      <section className={styles.cardGrid}>
        {sections.map((section) => (
          <article key={section.title} className={styles.card}>
            <p className={styles.cardLabel}>{section.title}</p>
            <h2>{section.title}</h2>
            <p>{section.body}</p>
          </article>
        ))}
      </section>

      <section className={styles.panel}>
        <div>
          <p className={styles.panelLabel}>First checklist</p>
          <h2>着手時に整理しておく項目</h2>
        </div>
        <ul className={styles.list}>
          {checklist.map((item) => (
            <li key={item}>{item}</li>
          ))}
        </ul>
      </section>
    </main>
  );
}
