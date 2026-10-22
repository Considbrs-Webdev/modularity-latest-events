---
name: security-check
description: Review the codebase or a change for common security problems - leaked secrets, missing validation and escaping, authorization gaps, injection, unsafe dependencies, personal data in logs. Use when the user asks for a security review or before releasing sensitive changes.
---

# Security check

Report findings; do not change code unless asked. Never print the value of a secret you find:
refer to the file, line and type only.

## 1. Scope and context

Whole repo or a diff? Read `AGENTS.md` for stated data restrictions and the architecture
(where input enters, where data is stored, who is authenticated).

## 2. Check

- **Secrets:** hard-coded keys, passwords, tokens, private keys, committed `.env` files,
  secrets in config, CI files and git history of the changed files. Check `.gitignore`.
- **Input and output:** unvalidated input, SQL/command/template injection, XSS and missing
  output escaping, path traversal, unsafe deserialization, SSRF, open redirects, file upload
  handling. Apply framework-specific rules (for WordPress: nonces, capability checks,
  sanitize on input, escape on output, `$wpdb->prepare`).
- **Authentication and authorization:** endpoints, REST routes, AJAX handlers and admin
  actions without permission checks; trust of client-supplied IDs or roles.
- **Data protection:** personal or customer data in logs, error messages, fixtures or test
  data; data sent to third parties.
- **Dependencies:** run the ecosystem's audit command and report vulnerable packages.
- **Configuration:** debug modes on, permissive CORS, insecure defaults, missing security
  headers, over-broad permissions.

## 3. Report

Group findings as **Critical**, **High**, **Medium**, **Low**, with `path:line`, the risk in
one sentence, how it could be exploited at a conceptual level, and a recommended fix. Mark
confidence. Say explicitly what you did not check. If a secret was committed, recommend
rotating it, since removing it from the code does not remove it from history.
