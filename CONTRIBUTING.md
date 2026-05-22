# Contributing

This repository is part of [Web Stack Defense](https://www.webstackdefense.com). Content here is curated to maintain consistency with the platform's editorial standards.

Issues and curated pull requests are allowed, but this is not an open-ended community repository where every submission will be merged.

## Before Opening an Issue

- Check the existing issues to confirm the topic is not already covered
- Confirm the issue is about content in this repository, not about WordPress core, plugins, or themes
- For security issues in the content itself, follow [SECURITY.md](SECURITY.md) instead

## Before Opening a Pull Request

Open an issue first to discuss scope. Pull requests submitted without prior discussion are unlikely to be merged.

## Contribution Standards

All contributions must:

- Be sanitized of any real domain names, IP addresses, account identifiers, or credentials
- Use the documentation reserved range `203.0.113.0/24` for IP examples
- Use `example.com` for domain examples
- Use clearly marked placeholder variables for any credentials, keys, or secrets
- Include source attribution where derived from external work
- Match the existing file structure and naming conventions
- Include inline comments explaining the reason for each configuration directive
- Be tested against at least one of the environments documented in [TESTED_ON.md](TESTED_ON.md)

## What Is Out of Scope

- WordPress plugin or theme code
- Generic SEO content
- Tool comparison reviews
- Tutorials that duplicate official WordPress documentation
- Content already covered by [Web Stack Defense](https://www.webstackdefense.com) guides

## Scope Reminder

This repository covers configuration-level WordPress hardening only. Application-layer security, custom plugin auditing, and incident response are outside the scope of this repository and are handled in other Web Stack Defense repositories.
