import { type ContactField, type ContactFieldType } from "../content";
import styles from "../page.module.css";

type ContactFormProps = {
  formFields: readonly ContactField[];
  submitLabel: string;
  submitMessage: string | null;
  formTitle: string;
  formDescription: string;
};

export function ContactForm({
  formFields,
  submitLabel,
  submitMessage,
  formTitle,
  formDescription,
}: ContactFormProps) {
  const selectOptionsPlaceholder = "選択してください";

  return (
    <div className={styles.formCard} data-reveal="up" data-delay={140}>
      <div className={styles.formIntro}>
        <h3>{formTitle}</h3>
        <p>{formDescription}</p>
      </div>

      {submitMessage ? <p className={styles.formMessage}>{submitMessage}</p> : null}

      <form id="contact-form" className={styles.form} action="/api/contact" method="post">
        <label className={styles.honeypot} aria-hidden>
          <span>内容入力エリア（自動送信対策）</span>
          <input type="text" name="hp" tabIndex={-1} autoComplete="off" />
        </label>

        {formFields.map((field) => {
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
          {submitLabel}
        </button>
      </form>
    </div>
  );
}
