---
name: onboarding
description: Explain this codebase to a developer who is new to it - purpose, architecture, important flows, where things live, how to run and debug locally - and answer follow-up questions. Use when someone is new to the project, asks "how does this work", "where is X done", or asks for an introduction or walkthrough.
---

# Onboarding

Goal: get a new developer productive, with an accurate picture of the system rather than a
generic one. Everything you say must come from reading the code; if you are inferring, say so.

## 1. Calibrate

Ask briefly, once: the person's experience with the main language/framework, and what they
will work on first (a feature area, bugs, everything). Adapt depth to the answer. If they
do not want to answer, assume an experienced developer new to this project.

## 2. Read before explaining

Start with `AGENTS.md`, `README.md` and `docs/`. Then verify against the code: entry points,
routing/hook registration, data models, configuration, build and test setup. Where the
documents and the code disagree, the code wins; note the discrepancy for step 5.

## 3. Walk through the system

Give a structured overview, kept to what matters:
- What the system does and for whom.
- Main building blocks and how they talk to each other (a small ASCII or Mermaid diagram
  is welcome).
- The two or three most important flows, followed through real files with `path:line`
  references.
- Where things live: directory map, naming patterns, where to add a new feature of each
  common kind.
- External dependencies and integrations: services, APIs, queues, environments.
- Conventions that are not obvious: see `docs/CODING_STANDARDS.md` if present.
- Gotchas: generated code, legacy areas, things that look wrong but are intentional.

## 4. Get them running

Walk through install, run, test and debug locally using the real commands. Run the safe ones
(install, test) to confirm they work, and report anything that fails. Never read or print
secrets; say which environment variables are needed and where they are supplied.

## 5. Close the loop

Offer a short "first tasks" list suited to what they said they will work on. List
discrepancies you found between docs and code, and offer to fix them (the `docs-update`
skill does this). Invite questions and answer each from the code with references.
