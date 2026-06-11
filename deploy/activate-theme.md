# Theme activation and rollback

## Normal release
1. Upload the family validation build to `wp-content/themes/lian-heart-custom-theme-clean-family-next` using `push-theme-via-sftp.ps1`.
2. Keep the existing `lian-heart-custom-theme-clean-next` folder untouched as the comparison target.
3. Verify the remote family folder contains:
   - `style.css`
   - `functions.php`
   - `front-page.php`
   - `assets/css/front-page.css`
   - `assets/js/front-page.js`
4. In WordPress admin, open `外観 > テーマ`.
5. Activate `リアンハート 家族向け改修検証`.
6. Verify the public site before removing any prior folder.

## Rollback
1. Keep the previous active theme folder as `lian-heart-custom-theme-clean-prev-YYYYMMDD-HHmm`.
2. If the family validation build regresses, re-activate the previous theme immediately in WordPress admin.
3. Leave `lian-heart-custom-theme-clean-next` in place until the family validation build is confirmed on desktop and mobile.
4. Do not delete `current / prev / family-next` folders until rollback is no longer needed.
