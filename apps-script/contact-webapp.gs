/**
 * Contact form endpoint for Google Apps Script.
 */

const CONFIG = {
  sheetName: "入居相談",
  sheetIdProperty: "SHEET_ID",
  notifyToProperty: "NOTIFY_TO",
};

const SHEET_COLUMNS = [
  "timestamp",
  "name",
  "furigana",
  "phone",
  "email",
  "relationship",
  "area",
  "facilityType",
  "careLevel",
  "budget",
  "moveInDate",
  "message",
  "privacy",
  "sourceUrl",
  "userAgent",
];

function doGet() {
  return ContentService.createTextOutput("ok").setMimeType(ContentService.MimeType.TEXT);
}

function doPost(e) {
  const payload = parsePayload(e);
  if (!payload) {
    return jsonResponse({ ok: false, error: "invalid payload" });
  }

  const props = PropertiesService.getScriptProperties();
  const sheetId = props.getProperty(CONFIG.sheetIdProperty);
  const notifyTo = props.getProperty(CONFIG.notifyToProperty) || "info@example.co.jp";

  if (!sheetId) {
    return jsonResponse({ ok: false, error: "SHEET_ID is not configured" });
  }

  try {
    const sheet = getOrCreateSheet(sheetId, CONFIG.sheetName);

    sheet.appendRow([
      payload.timestamp || "",
      payload.name || "",
      payload.furigana || "",
      payload.phone || "",
      payload.email || "",
      payload.relationship || "",
      payload.area || "",
      payload.facilityType || "",
      payload.careLevel || "",
      payload.budget || "",
      payload.moveInDate || "",
      payload.message || "",
      payload.privacy || "",
      payload.sourceUrl || "",
      payload.userAgent || "",
    ]);

    const subject = `リアンハート入居相談: ${payload.name || "ご依頼"}`;
    MailApp.sendEmail({
      to: notifyTo,
      subject,
      htmlBody: buildMailBody(payload),
    });

    return jsonResponse({ ok: true });
  } catch (error) {
    return jsonResponse({ ok: false, error: String(error) });
  }
}

function getOrCreateSheet(sheetId, sheetName) {
  const spreadsheet = SpreadsheetApp.openById(sheetId);
  let sheet = spreadsheet.getSheetByName(sheetName);

  if (!sheet) {
    sheet = spreadsheet.insertSheet(sheetName);
    sheet.appendRow(SHEET_COLUMNS);
    return sheet;
  }

  if (sheet.getLastColumn() < SHEET_COLUMNS.length) {
    sheet.insertColumnsAfter(
      sheet.getMaxColumns(),
      SHEET_COLUMNS.length - sheet.getMaxColumns(),
    );
  }

  if (sheet.getLastRow() === 0) {
    sheet.appendRow(SHEET_COLUMNS);
  } else {
    const currentHeader = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getDisplayValues()[0];
    if (currentHeader.join("") !== SHEET_COLUMNS.join("")) {
      sheet.insertRows(1, 1);
      sheet.getRange(1, 1, 1, SHEET_COLUMNS.length).setValues([SHEET_COLUMNS]);
    }
  }

  return sheet;
}

function parsePayload(e) {
  if (!e || !e.postData) return null;

  try {
    return JSON.parse(e.postData.contents || "{}");
  } catch (error) {
    return null;
  }
}

function buildMailBody(payload) {
  const safe = (value) => escapeHtml(value || "");

  return [
    "<h2>入居相談フォーム受付</h2>",
    "<ul>",
    `<li>受信日時: ${safe(payload.timestamp)}</li>`,
    `<li>お名前: ${safe(payload.name)}</li>`,
    `<li>フリガナ: ${safe(payload.furigana)}</li>`,
    `<li>電話番号: ${safe(payload.phone)}</li>`,
    `<li>メール: ${safe(payload.email)}</li>`,
    `<li>ご本人との関係: ${safe(payload.relationship)}</li>`,
    `<li>希望エリア: ${safe(payload.area)}</li>`,
    `<li>希望する施設の種類: ${safe(payload.facilityType)}</li>`,
    `<li>介護度: ${safe(payload.careLevel)}</li>`,
    `<li>ご予算: ${safe(payload.budget)}</li>`,
    `<li>入居希望時期: ${safe(payload.moveInDate)}</li>`,
    `<li>入居相談内容: ${safe(payload.message)}</li>`,
    `<li>プライバシーポリシー同意: ${safe(payload.privacy)}</li>`,
    `<li>送信元URL: ${safe(payload.sourceUrl)}</li>`,
    `<li>ユーザーエージェント: ${safe(payload.userAgent)}</li>`,
    "</ul>",
  ].join("");
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/\"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function jsonResponse(payload) {
  const output = ContentService.createTextOutput(JSON.stringify(payload));
  output.setMimeType(ContentService.MimeType.JSON);
  return output;
}
