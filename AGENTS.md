# Repository Agent Browser Workflow

## Purpose
This repository uses `agent-browser` for browser automation when a site requires login or when the user's local Chrome session must be reused.

## Default rule
- If the user says they are already logged in, prefer `agent-browser --auto-connect`.
- Do not ask for account credentials first if `Chrome（デバッグ）` can be used.
- Treat the user's normal browser session and Playwright session as different unless `agent-browser --auto-connect` is explicitly used.

## Expected local environment
- OS: Windows 11
- `agent-browser` is installed globally
- `Chrome（デバッグ）` shortcut exists on the desktop and launches Chrome with `--remote-debugging-port=9222`

## Standard workflow
1. Ask the user to open `Chrome（デバッグ）`.
2. Ask the user to log in there manually.
3. Connect with:
   - `npx agent-browser --auto-connect snapshot`
4. Use the same `--auto-connect` session for:
   - DOM inspection
   - screenshots
   - clicks / fills / scrolls
   - authenticated page analysis

## Typical commands
- Snapshot:
  - `npx agent-browser --auto-connect snapshot`
- Interactive-only snapshot:
  - `npx agent-browser --auto-connect snapshot -i`
- Current URL:
  - `npx agent-browser --auto-connect get url`
- Full screenshot:
  - `npx agent-browser --auto-connect screenshot --full ./full-page.png`
- Eval:
  - `npx agent-browser --auto-connect eval "<javascript>"`

## When not to use Playwright first
- Xserver, admin panels, SSO portals, or any site where the user is already logged in through the debug Chrome profile
- Sites where the user explicitly wants the agent to reuse their current authenticated session

## Security
- Prefer user login via debug Chrome over sharing credentials in chat
- If auth state is saved, do not commit it
- Authentication files and session exports are sensitive

## Reference
- Detailed local guide: `./agent-browser-for-ai.md`

