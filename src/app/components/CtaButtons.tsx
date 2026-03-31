import styles from "../page.module.css";

type Cta = {
  readonly label: string;
  readonly href: string;
};

export function CtaButtons({ ctas }: { ctas: readonly Cta[] }) {
  return (
    <>
      {ctas.map((cta, index) => (
        <a
          key={cta.label}
          className={
            index === 0
              ? styles.primaryAction
              : index === 1
                ? styles.secondaryAction
                : styles.ghostAction
          }
          href={cta.href}
          target={cta.href.startsWith("http") ? "_blank" : undefined}
          rel={cta.href.startsWith("http") ? "noreferrer" : undefined}
        >
          {cta.label}
        </a>
      ))}
    </>
  );
}
