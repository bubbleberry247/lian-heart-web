import { lpContent, type ContactField, type ContactFieldType } from "./content";
import styles from "./page.module.css";

type SearchParams = {
  contact?: "success" | "error";
};

type CompanyInfoItem = {
  key: string;
  label: string;
  value: string;
};

export default async function Home({
  searchParams,
}: {
  searchParams?: Promise<SearchParams>;
}) {
  const params = searchParams ? await searchParams : undefined;
  const submitStatus = params?.contact;
  const submitMessage =
    submitStatus === "success"
      ? lpContent.contact.successMessage
      : submitStatus === "error"
        ? lpContent.contact.errorMessage
        : null;

  const structuredData = {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        name: lpContent.brand,
        url: process.env.NEXT_PUBLIC_SITE_URL || "https://example.com",
        areaServed: lpContent.serviceArea,
      },
      {
        "@type": "LocalBusiness",
        name: lpContent.brand,
        description: lpContent.metadata.description,
        areaServed: lpContent.serviceArea,
        telephone: lpContent.company.phone,
        email: lpContent.company.email,
        address: {
          "@type": "PostalAddress",
          postalCode: lpContent.company.postalCode.replace("〒", ""),
          addressLocality: lpContent.company.addressLocality,
          addressRegion: lpContent.company.addressRegion,
          addressCountry: "JP",
          streetAddress: lpContent.company.address,
        },
        contactPoint: {
          "@type": "ContactPoint",
          contactType: "customer support",
          telephone: lpContent.company.phone,
          email: lpContent.company.email,
          areaServed: lpContent.serviceArea,
        },
      },
      {
        "@type": "WebSite",
        name: lpContent.brand,
        description: lpContent.metadata.description,
        url: process.env.NEXT_PUBLIC_SITE_URL || "https://example.com",
      },
    ],
  };

  const companyInfo: CompanyInfoItem[] = [
    { key: "name", label: "会社名", value: lpContent.company.name },
    { key: "representative", label: "代表者", value: lpContent.company.representative },
    { key: "address", label: "所在地", value: `${lpContent.company.postalCode} ${lpContent.company.address}` },
    { key: "phone", label: "電話番号", value: lpContent.company.phone },
    { key: "fax", label: "FAX", value: lpContent.company.fax },
    { key: "email", label: "メール", value: lpContent.company.email },
    { key: "hours", label: "営業時間", value: lpContent.company.businessHours },
    { key: "holiday", label: "定休日", value: lpContent.company.holidays },
    { key: "business", label: "事業内容", value: lpContent.company.businessContent },
  ];

  const selectOptionsPlaceholder = "選択してください";

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(structuredData) }}
      />

      <main className={styles.page}>
        <header className={styles.header}>
          <a className={styles.logo} href="#">
            {lpContent.brand}
          </a>

          <nav className={styles.nav} aria-label="ページ内ナビゲーション">
            {lpContent.navigation.map((item) => (
              <a key={item.href} href={item.href}>
                {item.label}
              </a>
            ))}
          </nav>
        </header>

        <section className={styles.hero}>
          <div className={styles.heroBody}>
            <p className={styles.eyebrow}>{lpContent.hero.eyebrow}</p>
            <h1>{lpContent.hero.title}</h1>
            <p className={styles.lead}>{lpContent.hero.description}</p>

            <ul className={styles.highlightList}>
              {lpContent.hero.chips.map((item) => (
                <li key={item}>{item}</li>
              ))}
            </ul>

            <div className={styles.actions}>
              {lpContent.ctas.map((cta, index) => (
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
            </div>
          </div>

          <div className={`${styles.visualCard} ${styles.heroVisual}`} aria-label="ヒーロー画像（プレースホルダー）" />
        </section>

        <section id="concept" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.concept.label}</p>
            <h2>{lpContent.concept.title}</h2>
          </div>

          <div className={styles.splitPanel}>
            <div className={styles.copyBlock}>
              <p className={styles.copyLead}>{lpContent.concept.lead}</p>
              {lpContent.concept.body.map((paragraph) => (
                <p key={paragraph}>{paragraph}</p>
              ))}
            </div>

            <div
              className={`${styles.visualCard} ${styles.conceptVisual}`}
              aria-label="コンセプト画像（プレースホルダー）"
            />
          </div>
        </section>

        <section id="pride" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.features.label}</p>
            <h2>{lpContent.features.title}</h2>
            <p>{lpContent.features.intro}</p>
          </div>

          <div className={styles.cardGrid}>
            {lpContent.features.items.map((item) => (
              <article key={item.title} className={styles.card}>
                <h3>{item.title}</h3>
                <p>{item.body}</p>
              </article>
            ))}
          </div>
        </section>

        <section id="menu" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.flow.label}</p>
            <h2>{lpContent.flow.title}</h2>
          </div>

          <div className={styles.flowGrid}>
            {lpContent.flow.items.map((item) => (
              <article key={item.title} className={styles.flowCard}>
                <h3>{item.title}</h3>
                <p>{item.body}</p>
              </article>
            ))}
          </div>
          <p className={styles.sampleNote}>{lpContent.flow.note}</p>
        </section>

        <section id="greeting" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.greeting.label}</p>
            <h2>{lpContent.greeting.title}</h2>
          </div>

          <div className={styles.splitPanel}>
            <div
              className={`${styles.visualCard} ${styles.portraitCard}`}
              aria-label="代表写真（プレースホルダー）"
            />

            <div className={styles.copyBlock}>
              <p className={styles.personMeta}>
                {lpContent.greeting.role ? `${lpContent.greeting.role} / ` : ""}
                {lpContent.greeting.name}
              </p>
              {lpContent.greeting.body.map((paragraph) => (
                <p key={paragraph}>{paragraph}</p>
              ))}
            </div>
          </div>
        </section>

        <section id="qa" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.faq.label}</p>
            <h2>{lpContent.faq.title}</h2>
          </div>

          <div className={styles.faqList}>
            {lpContent.faq.items.map((item) => (
              <details key={item.question} className={styles.faqItem}>
                <summary>{item.question}</summary>
                <p>{item.answer}</p>
              </details>
            ))}
          </div>
        </section>

        <section id="facility" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.facilities.label}</p>
            <h2>{lpContent.facilities.title}</h2>
            <p>{lpContent.facilities.intro}</p>
          </div>

          <div className={styles.cardGrid}>
            {lpContent.facilities.items.map((item) => (
              <article key={item.title} className={styles.card}>
                <h3>{item.title}</h3>
                <p>{item.body}</p>
              </article>
            ))}
          </div>
        </section>

        <section id="shop-info" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.company.label}</p>
            <h2>{lpContent.company.title}</h2>
            <p>{lpContent.company.intro}</p>
          </div>

          <div className={styles.companyCard}>
            <div className={styles.companyGrid}>
              {companyInfo.map((item) => (
                <div key={item.key}>
                  <p className={styles.infoLabel}>{item.label}</p>
                  {item.key === "phone" ? (
                    <a href={`tel:${item.value}`}>{item.value}</a>
                  ) : item.key === "email" ? (
                    <a href={`mailto:${item.value}`}>{item.value}</a>
                  ) : (
                    <p>{item.value}</p>
                  )}
                </div>
              ))}
            </div>

            <p className={styles.sampleNote}>{lpContent.company.note}</p>
          </div>
        </section>

        <section id="contact" className={styles.section}>
          <div className={styles.sectionHeader}>
            <p className={styles.sectionLabel}>{lpContent.contact.label}</p>
            <h2>{lpContent.contact.title}</h2>
            <p>{lpContent.contact.lead}</p>
            <p className={styles.urgentMessage}>{lpContent.contact.urgentMessage}</p>
          </div>

          <div className={styles.contactLayout}>
            <div className={styles.contactCard}>
              <div className={styles.ctaStack}>
                {lpContent.ctas.map((cta, index) => (
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
              </div>
            </div>

            <div className={styles.formCard}>
              <div className={styles.formIntro}>
                <h3>{lpContent.contact.formTitle}</h3>
                <p>{lpContent.contact.formDescription}</p>
              </div>

              {submitMessage ? <p className={styles.formMessage}>{submitMessage}</p> : null}

              <form id="contact-form" className={styles.form} action="/api/contact" method="post">
                <label className={styles.honeypot} aria-hidden>
                  <span>内容入力エリア（自動送信対策）</span>
                  <input type="text" name="hp" tabIndex={-1} autoComplete="off" />
                </label>

                {lpContent.contact.formFields.map((field) => {
                  const formField = field as ContactField;
                  const fieldType = formField.type as ContactFieldType;

                  if (fieldType === "textarea") {
                    return (
                      <label key={formField.name} className={styles.field}>
                        <span>{formField.label}</span>
                        <textarea
                          name={formField.name}
                          rows={5}
                          required={formField.required}
                          placeholder={formField.placeholder}
                        />
                      </label>
                    );
                  }

                  if (fieldType === "checkbox") {
                    return (
                      <label key={formField.name} className={styles.fieldCheckbox}>
                        <input
                          name={formField.name}
                          type="checkbox"
                          required={formField.required}
                          value="同意"
                        />
                        <span>{formField.label}</span>
                      </label>
                    );
                  }

                  if (fieldType === "select") {
                    return (
                      <label key={formField.name} className={styles.field}>
                        <span>{formField.label}</span>
                        <select name={formField.name} required={formField.required} defaultValue="">
                          <option value="" disabled>
                            {selectOptionsPlaceholder}
                          </option>
                          {formField.options?.map((option) => (
                            <option key={option.value} value={option.value}>
                              {option.label}
                            </option>
                          ))}
                        </select>
                      </label>
                    );
                  }

                  return (
                    <label key={formField.name} className={styles.field}>
                      <span>{formField.label}</span>
                      <input
                        name={formField.name}
                        type={fieldType}
                        required={formField.required}
                        placeholder={formField.placeholder}
                      />
                    </label>
                  );
                })}

                <button className={styles.submitButton} type="submit">
                  {lpContent.contact.submitLabel}
                </button>
              </form>
            </div>
          </div>
        </section>

        <footer className={styles.footer}>
          <div>
            <a className={styles.logo} href="#">
              {lpContent.footer.logoText}
            </a>
            <p className={styles.footerCopy}>{lpContent.footer.contactText}</p>
          </div>

          <div className={styles.footerLinks}>
            {lpContent.navigation.map((item) => (
              <a key={item.href} href={item.href}>
                {item.label}
              </a>
            ))}
          </div>

          <p className={styles.sampleNote}>{lpContent.footer.smallPrint}</p>
          <p className={styles.footerCopyright}>{lpContent.footer.copyright}</p>
        </footer>
      </main>
    </>
  );
}
