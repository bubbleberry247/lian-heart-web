# Local Windows setup for agent-browser auto-connect

## Expected environment

- OS: Windows 11
- `agent-browser` is installed globally
- Chrome debug shortcut exists on the desktop
- Debug Chrome uses remote debugging on port `9222`

## Primary pattern

```bash
npx agent-browser --auto-connect snapshot
```

Use this when the user has already opened debug Chrome and logged in manually.

## Common commands

### Inspect

```bash
npx agent-browser --auto-connect snapshot
npx agent-browser --auto-connect snapshot -i
npx agent-browser --auto-connect eval "<javascript>"
```

### Screenshot

```bash
npx agent-browser --auto-connect screenshot ./screenshot.png
npx agent-browser --auto-connect screenshot --full ./full-page.png
npx agent-browser --auto-connect screenshot --annotate ./annotated.png
```

### Operate

```bash
npx agent-browser --auto-connect click @e15
npx agent-browser --auto-connect fill @e20 "text"
npx agent-browser --auto-connect press Enter
npx agent-browser --auto-connect scroll down 500
```

### Session helpers

```bash
npx agent-browser --auto-connect get url
npx agent-browser --auto-connect get title
npx agent-browser close
```

## Notes

- Do not assume Windows Credential Manager will be visible to agent-browser.
- Prefer auto-connect over asking for credentials.
- If auto-connect fails, verify the user launched debug Chrome instead of normal Chrome.
- Authentication state exports are sensitive and must not be committed.
