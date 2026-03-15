---
name: agent-browser-auto-connect
description: Reuse the user's authenticated browser session through agent-browser auto-connect instead of asking for credentials first. Use when the user says they are already logged in, mentions debug Chrome, asks to inspect or operate a logged-in site, or needs authenticated browser work on Xserver, admin panels, SSO portals, or other sites where the local browser session should be preserved.
---

# Agent Browser Auto Connect

Use this skill when the user is already logged in on their machine and the work should continue inside that same authenticated session.

## Core rule

- Prefer `agent-browser --auto-connect` before asking for IDs or passwords.
- Treat Playwright and the user's normal browser as separate sessions unless `agent-browser --auto-connect` is used.
- Assume the intended workflow is:
  1. User opens debug Chrome
  2. User logs in manually
  3. Codex connects to that session and continues the task

## Workflow

1. Confirm the task actually needs the user's logged-in browser state.
2. Ask the user to open debug Chrome and log in there if they have not already.
3. Connect with `agent-browser --auto-connect`.
4. Use the connected session for inspection, screenshots, clicks, fills, scrolling, and authenticated analysis.
5. Only ask for credentials if auto-connect is unavailable or the user explicitly wants that fallback.

## Typical use cases

- Xserver or other hosting control panels
- WordPress admin screens already open in the user's browser
- Corporate portals with SSO or 2FA
- Any site where the user says "I already logged in" or "use my current browser session"

## Command pattern

- Start with:
  - `npx agent-browser --auto-connect snapshot`
- Then use the same session for:
  - `snapshot -i`
  - `eval`
  - `screenshot`
  - `click`
  - `fill`
  - `scroll`

## When to read more

- Read `references/local-windows-setup.md` if you need:
  - exact local environment assumptions
  - common command examples
  - Windows-specific notes for debug Chrome
