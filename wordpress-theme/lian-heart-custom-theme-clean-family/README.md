# Lian Heart Family Validation Theme

This directory contains the family-focused validation theme for the Lian Heart facility introduction site.

## Install path

`wp-content/themes/lian-heart-custom-theme-clean-family`

## Requirements

- WordPress 6.4+
- PHP 8.1+
- ACF Pro
- Google Apps Script Web App URL

## Setup

1. Copy this folder into `wp-content/themes/`.
2. Activate the family validation theme shown in WordPress admin.
3. Enable `ACF Pro`.
4. Confirm that the `medical-care-professionals` page exists after theme activation.
5. Verify the family-focused front page and the `SUPPORT` section.

## Included changes

- Family-first front page copy
- `SUPPORT` partner guidance section after `Flow03`
- Remote-family / reconsultation / transfer wording
- Footer and support-section link for referrers
- `medical-care-professionals` page template and auto-create hook

## Notes

- Keep `lian-heart-custom-theme-clean` and `lian-heart-custom-theme-clean-next` for comparison and rollback.
- Use `deploy/build-theme-artifact.ps1` and `deploy/push-theme-via-sftp.ps1` with the family theme defaults.
