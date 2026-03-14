export type ContactFieldType = "text" | "tel" | "email" | "textarea" | "checkbox" | "select";

export type ContactFieldOption = {
  label: string;
  value: string;
};

export type ContactField = {
  name: string;
  label: string;
  type: ContactFieldType;
  required: boolean;
  placeholder?: string;
  options?: ContactFieldOption[];
};

export const lpContent = {
  brand: "リアンハート",
  serviceArea: "愛知県全域",
  metadata: {
    title: "愛知県の老人ホーム紹介・入居相談 | リアンハート",
    description:
      "愛知県全域で老人ホーム紹介・介護施設紹介・入居相談をサポート。介護度、医療的な配慮、費用、立地、ご家族の希望を整理し、見学・比較検討まで伴走します。",
  },
  navigation: [
    { label: "コンセプト", href: "#concept" },
    { label: "サービスの特徴", href: "#pride" },
    { label: "入居相談の流れ", href: "#menu" },
    { label: "代表挨拶", href: "#greeting" },
    { label: "よくある質問", href: "#qa" },
    { label: "施設の種類", href: "#facility" },
    { label: "運営会社", href: "#shop-info" },
    { label: "入居相談", href: "#contact" },
  ],
  ctas: [
    { label: "電話で入居相談する", href: "tel:052-000-0000" },
    { label: "LINEで入居相談する", href: "https://line.me/ti/p/@sample" },
    { label: "入居相談フォームへ", href: "#contact" },
  ],
  hero: {
    eyebrow: "愛知県全域対応の老人ホーム紹介・入居相談",
    title:
      "愛知で老人ホーム紹介をご希望の方へ。リアンハートが入居相談から見学・比較まで丁寧に伴走します。",
    description:
      "介護施設紹介では、介護度、医療的な配慮、費用、立地、ご家族の通いやすさを整理しながら候補をご提案します。入居相談の段階から見学・比較検討まで伴走し、入居後の「思っていたのと違った」を減らすためのご提案を行います。",
    chips: ["愛知県全域対応", "老人ホーム紹介", "入居相談"],
    imageLabel: "ヒーロー画像（プレースホルダー）",
  },
  concept: {
    label: "コンセプト",
    title: "ご本人にもご家族にも、納得できる老人ホーム紹介を。",
    lead: "介護施設紹介は、空室や料金だけで決められるものではありません。",
    body: [
      "今の身体状況、必要な介護や医療的な配慮、これからの暮らし方、ご家族の通いやすさや予算。大切なのは、条件を一つずつ整理しながら、無理のない選択肢を見つけることです。",
      "リアンハートでは、愛知県内で老人ホーム紹介をご検討中の方へ、入居相談の段階から見学前の情報整理、比較検討まで伴走し、入居後の「思っていたのと違った」を減らすご提案を行います。",
    ],
    imageLabel: "コンセプト画像（プレースホルダー）",
  },
  features: {
    label: "サービスの特徴",
    title: "介護施設紹介で大切にしている3つのこと",
    intro:
      "リアンハートは、老人ホーム紹介を急いで決めるのではなく、入居相談の段階から条件整理と比較検討を丁寧に進めます。",
    items: [
      {
        title: "入居相談で条件を整理する",
        body: "介護度、医療的な配慮、費用、希望エリア、生活スタイルなどを確認し、ご状況に合う候補を整理します。",
      },
      {
        title: "老人ホーム紹介を比較しやすくする",
        body: "候補施設の見学調整だけでなく、確認すべきポイントを整理し、複数施設を比較しやすい状態で検討できるようサポートします。",
      },
      {
        title: "介護施設紹介でミスマッチを減らす",
        body: "設備や費用だけでなく、暮らし方や支援体制との相性まで事前に確認し、納得して選べるようお手伝いします。",
      },
    ],
  },
  flow: {
    label: "入居相談の流れ",
    title: "入居相談から老人ホーム紹介までの流れ",
    items: [
      {
        title: "入居相談",
        body: "現在の生活状況、希望エリア、予算、入居時期、ご家族のご希望などを整理します。",
      },
      {
        title: "老人ホーム紹介",
        body: "整理した条件をもとに、検討しやすい候補をご案内します。",
      },
      {
        title: "見学・比較のサポート",
        body: "見学日程の調整を行い、見るべきポイントや比較ポイントをわかりやすく整理します。",
      },
      {
        title: "入居前の確認",
        body: "候補が絞れた後も、入居前に確認しておきたい点を整理しながら進めます。",
      },
    ],
    note: "※ご案内は提携状況や受入条件に応じて行います。",
  },
  greeting: {
    label: "代表挨拶",
    title: "代表挨拶",
    name: "代表 山田 太郎（サンプル）",
    role: "代表",
    body: [
      "はじめまして。リアンハート 代表の山田太郎です。",
      "老人ホーム紹介や介護施設紹介は、空室や費用だけで決められるものではありません。ご本人の状態、ご家族の不安、これからの暮らし方まで整理しながら、一つずつ判断していくことが大切だと考えています。",
      "リアンハートでは、比較しやすい形で情報を整理し、納得できる選択につながるよう入居相談から伴走します。愛知県内でご検討の際は、お気軽にご連絡ください。",
    ],
    imageLabel: "代表写真（プレースホルダー）",
  },
  faq: {
    label: "よくある質問",
    title: "よくある質問",
    items: [
      {
        question: "まだ何も決まっていない段階でも相談できますか？",
        answer:
          "はい。情報収集の段階からご相談いただけます。早めに条件を整理しておくことで、必要になったときに慌てず判断しやすくなります。",
      },
      {
        question: "愛知県のどこまで対応していますか？",
        answer:
          "名古屋市をはじめ、尾張・知多・西三河・東三河など、愛知県全域でご相談を承ります。",
      },
      {
        question: "どのような施設を紹介してもらえますか？",
        answer:
          "介護付有料老人ホーム、住宅型有料老人ホーム、サービス付き高齢者向け住宅、グループホームなど、ご状況に応じた候補をご案内します。",
      },
      {
        question: "施設見学の日程調整はお願いできますか？",
        answer:
          "はい。候補施設の見学日程を調整し、比較しやすいよう確認ポイントも整理します。",
      },
      {
        question: "医療的な配慮が必要な場合でも相談できますか？",
        answer:
          "はい。現在の状況や必要な支援内容を確認したうえで、受入条件を踏まえて候補を検討します。",
      },
      {
        question: "家族だけで相談しても大丈夫ですか？",
        answer: "はい。ご本人がすぐに動けない場合や、まずはご家族で情報整理したい場合もご相談いただけます。",
      },
      {
        question: "すべての施設を紹介してもらえますか？",
        answer:
          "提携状況や対応条件に応じてご案内します。ご希望に合わせて候補をご提案しますが、県内すべての施設を網羅的にご紹介するものではありません。",
      },
      {
        question: "相談時に何を伝えればよいですか？",
        answer:
          "希望エリア、月額予算、介護度、現在の生活状況、入居希望時期などが分かる範囲であるとスムーズです。未定でも問題ありません。",
      },
    ],
  },
  facilities: {
    label: "施設の種類",
    title: "ご紹介可能な施設の種類",
    intro:
      "老人ホーム紹介・介護施設紹介では、ご本人の状態やご希望に応じて、以下のような施設種別から候補をご案内します。",
    items: [
      {
        title: "介護付有料老人ホーム",
        body: "日常的な介護を受けながら生活したい方に向けた候補です。",
      },
      {
        title: "住宅型有料老人ホーム",
        body: "生活支援を受けながら、必要に応じて外部サービスの利用を検討したい方に向けた候補です。",
      },
      {
        title: "サービス付き高齢者向け住宅",
        body: "見守りや生活相談を受けながら、自分らしい暮らしを続けたい方に向けた候補です。",
      },
      {
        title: "グループホーム",
        body: "少人数の環境で落ち着いて生活したい方に向けた候補です。",
      },
    ],
  },
  company: {
    label: "運営会社",
    title: "運営会社",
    intro: "会社名のみ実名、その他はサンプル情報です。実際の情報に差し替えてご利用ください。",
    name: "リアンハート",
    representative: "代表 山田 太郎（サンプル）",
    postalCode: "460-0000",
    addressRegion: "愛知県",
    addressLocality: "名古屋市",
    address: "名古屋市中区○○1-2-3 ○○ビル5F",
    phone: "052-000-0000",
    fax: "052-000-0001",
    email: "info@example.co.jp",
    businessHours: "9:00-18:00",
    holidays: "土日祝",
    businessContent: "介護施設紹介 / 老人ホーム紹介 / 入居相談",
    note: "※会社名以外の掲載情報はサンプルです。実際の情報に差し替えてください。",
  },
  contact: {
    label: "入居相談",
    title: "入居相談・お問い合わせ",
    lead:
      "老人ホーム紹介や介護施設紹介に関する入居相談を承っています。ご本人、ご家族、関係者の方からのご連絡に対応します。",
    urgentMessage: "お急ぎの場合は、お電話での入居相談をおすすめします。",
    formTitle: "入居相談フォーム",
    formDescription:
      "下記フォームからお問い合わせください。内容を確認のうえ、担当よりご連絡いたします。",
    successMessage:
      "入居相談ありがとうございます。内容を確認のうえ、担当よりご連絡いたします。",
    errorMessage: "送信に失敗しました。時間を置いて再度お試しください。",
    formFields: [
      {
        name: "name",
        label: "お名前",
        type: "text",
        required: true,
        placeholder: "山田 太郎",
      },
      {
        name: "furigana",
        label: "フリガナ",
        type: "text",
        required: true,
        placeholder: "ヤマダ タロウ",
      },
      {
        name: "phone",
        label: "電話番号",
        type: "tel",
        required: true,
        placeholder: "052-000-0000",
      },
      {
        name: "email",
        label: "メールアドレス",
        type: "email",
        required: false,
        placeholder: "info@example.co.jp",
      },
      {
        name: "relationship",
        label: "ご本人との関係",
        type: "text",
        required: false,
        placeholder: "本人・ご家族・関係者",
      },
      {
        name: "area",
        label: "希望エリア",
        type: "text",
        required: false,
        placeholder: "名古屋市内",
      },
      {
        name: "facilityType",
        label: "希望する施設の種類",
        type: "select",
        required: false,
        options: [
          { label: "介護付有料老人ホーム", value: "介護付有料老人ホーム" },
          { label: "住宅型有料老人ホーム", value: "住宅型有料老人ホーム" },
          { label: "サービス付き高齢者向け住宅", value: "サービス付き高齢者向け住宅" },
          { label: "グループホーム", value: "グループホーム" },
          { label: "その他", value: "その他" },
        ],
      },
      {
        name: "careLevel",
        label: "介護度",
        type: "select",
        required: false,
        options: [
          { label: "未指定", value: "未指定" },
          { label: "要支援1", value: "要支援1" },
          { label: "要支援2", value: "要支援2" },
          { label: "要介護1", value: "要介護1" },
          { label: "要介護2", value: "要介護2" },
          { label: "要介護3", value: "要介護3" },
          { label: "要介護4", value: "要介護4" },
          { label: "要介護5", value: "要介護5" },
        ],
      },
      {
        name: "budget",
        label: "ご予算",
        type: "select",
        required: false,
        options: [
          { label: "未指定", value: "未指定" },
          { label: "10万円未満", value: "10万円未満" },
          { label: "10〜15万円", value: "10〜15万円" },
          { label: "15〜20万円", value: "15〜20万円" },
          { label: "20〜25万円", value: "20〜25万円" },
          { label: "25万円以上", value: "25万円以上" },
        ],
      },
      {
        name: "moveInDate",
        label: "入居希望時期",
        type: "select",
        required: false,
        options: [
          { label: "未指定", value: "未指定" },
          { label: "1〜3か月以内", value: "1〜3か月以内" },
          { label: "3〜6か月以内", value: "3〜6か月以内" },
          { label: "6か月以上先", value: "6か月以上先" },
        ],
      },
      {
        name: "message",
        label: "入居相談内容",
        type: "textarea",
        required: true,
        placeholder: "ご本人の状況や希望内容、特に重視したい点をご記入ください。",
      },
      {
        name: "privacy",
        label: "プライバシーポリシーに同意する",
        type: "checkbox",
        required: true,
      },
    ],
    submitLabel: "入居相談を送信する",
  },
  footer: {
    logoText: "リアンハート",
    contactText: "リアンハート / TEL 052-000-0000 / MAIL info@example.co.jp",
    smallPrint: "※会社名以外の掲載情報はサンプルです。実際の情報に差し替えてください。",
    copyright: "© リアンハート All Rights Reserved.",
  },
} as const;
