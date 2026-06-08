# WMS Modernization Report

Date: 2026-06-08

## Modified Files

- `composer.json`
  - Set PHP support to `>=7.4 <8.4`.
  - Declared CodeIgniter `3.1.13`.
  - Declared Dompdf `^2.0` for PHP 7.4 through 8.3 compatibility.
  - Declared PhpSpreadsheet `^1.29`.
  - Removed obsolete dev dependencies that blocked production installs.
- `composer.lock`
  - Locked production dependencies, including CodeIgniter `3.1.13`, Dompdf `v2.0.8`, and PhpSpreadsheet `1.30.5`.
- `vendor/`
  - Installed production Composer dependencies.
  - `index.php` already prefers `vendor/codeigniter/framework/system` when present, so the runtime CodeIgniter system is now `3.1.13`.
- `application/libraries/Dompdf_gen.php`
  - Replaced the old dynamic `$CI->dompdf` adapter behavior with a contained modern Dompdf wrapper.
  - Disabled remote loading and embedded PHP execution in Dompdf options.
  - Defaulted stream behavior to `Attachment => 1`.
- `application/controllers/Website.php`
  - Updated Incoming Goods, Outgoing Goods, and Adjustment PDF methods to call `$this->dompdf_gen`.
  - Changed all three PDF streams from inline rendering to forced downloads with `Attachment => 1`.

## Removed Obsolete Files

- `templates/backend/assets/data/bootstrap_table_test.json`
- `templates/backend/assets/data/bootstrap_table_test2.json`
- `templates/backend/assets/less/components/example.less`
- `templates/backend/assets/less/examples/`
- `vendor/maennchen/zipstream-php/test/`
- `vendor/markbaker/complex/examples/`
- `vendor/markbaker/matrix/examples/`
- Temporary local files removed after install: `composer.phar`, `.composer-cache/`, and edit backup files.

No `application/third_party/dompdf/www` or `application/third_party/dompdf/www/test` folders were present in this copy of the project.

## Security Audit Report

Searched for: `eval(`, `base64_decode(`, `gzinflate(`, `shell_exec(`, `system(`, `passthru(`, `exec(`.

Safe framework/library usages:

- CodeIgniter system and vendor framework contain expected internal usages in `Upload`, `Image_lib`, `Session_database_driver`, `Encryption`, `Output`, `Loader`, `Xmlrpc`, and database drivers.
- Dompdf and its dependencies contain expected internal `base64_decode` and callback/evaluator code. Dompdf PHP execution is disabled in `application/libraries/Dompdf_gen.php` via `isPhpEnabled = FALSE`.
- PhpSpreadsheet contains expected password/hash decoding code.

Suspicious or review-required usages:

- `application/controllers/Website.php` uses `curl_exec()` for OneSignal notifications and includes hard-coded OneSignal identifiers/secrets. This is not malware, but secrets should be moved to environment/config values before production.
- Legacy `system/libraries/Encrypt.php` still exists as part of the CI framework tree and contains Mcrypt paths. The application scan found no active `$this->encrypt` or `load->library('encrypt')` usage under `application/`.

Required fixes completed:

- Removed vendor test/example folders containing non-runtime command-execution test code.
- Removed public sample/test frontend assets.
- Disabled Dompdf PHP execution.

Recommended remaining hardening:

- Move OneSignal keys out of source code.
- Keep `vendor/` blocked from direct web access. Current `.htaccess` blocks `/vendor`.
- Enable CSRF protection after testing forms, if the legacy workflows can support token handling.

## PHP 8 Compatibility Report

Completed:

- Runtime CodeIgniter is now `3.1.13` through Composer vendor system path.
- Modern Dompdf and PhpSpreadsheet dependencies are installed and autoloadable.
- No old PHPExcel folders were found under `application` or `system`.
- No old Dompdf third-party folder was found under `application/third_party`.
- No old `$str{0}`-style string offset syntax was found in `application`, `system`, or `vendor`.
- No active Mcrypt/Encrypt library usage was found in `application`.
- Modified PHP files passed syntax checks under local PHP 7.4.

Not fully verified locally:

- PHP 8.0, 8.1, 8.2, and 8.3 syntax/runtime checks were not available in this local XAMPP environment. Production should be smoke-tested on PHP 8.3 after upload.
- Local PHP CLI reports OpenSSL is loaded twice. Fix the duplicate OpenSSL extension line in XAMPP `php.ini` if desired.

## PDF Functionality Verification Report

Verified locally:

- `Dompdf\Dompdf` autoloads from `vendor/autoload.php`.
- `application/libraries/Dompdf_gen.php` passes PHP 7.4 syntax check.
- `application/controllers/Website.php` passes PHP 7.4 syntax check.
- Incoming Goods PDF now streams `incomingoods.pdf` with `Attachment => 1`.
- Outgoing Goods PDF now streams `outgoods.pdf` with `Attachment => 1`.
- Adjustment PDF now streams `in&outgoods.pdf` with `Attachment => 1`.

Not verified locally:

- Full browser PDF generation was not executed because the local database/server session was not started in this pass.

## Routing Cleanup Report

- Main application links and forms already use `site_url()` for controller methods.
- Static CSS, JS, image, font, upload, editor, finder, template, and captcha paths use `base_url()`, which is appropriate.
- PDF report download buttons already use `site_url()`.
- `.htaccess` supports clean URLs and blocks `application`, `system`, and `vendor` direct access.
- `index.php` routing remains supported because `config['index_page']` is `index.php`.

## Google Safe Browsing Cleanup Report

Completed:

- Removed public sample/test JSON files.
- Removed public LESS example folder and example file.
- Removed Composer vendor test/example folders that are not needed at runtime.
- Confirmed obsolete Dompdf public demo/test folders were not present.
- Confirmed no ZIP archives were found in the project during cleanup scan.

Recommended production steps:

- Upload only the cleaned tree.
- Do not upload local database files, old backup files, or temporary archives.
- After deployment, request a Google Safe Browsing review from Google Search Console once the production domain is clean.

## InfinityFree Deployment Guide

1. Upload the project files, including `vendor/`, `composer.json`, and `composer.lock`.
2. Keep `application/`, `system/`, and `vendor/` protected by `.htaccess`.
3. Confirm `application/config/config.php` uses `https://warehouse.freedev.app/` and keeps `index_page = 'index.php'`.
4. Confirm `application/config/database.php` uses the InfinityFree host, user, and database name.
5. Replace the placeholder database password with the password configured in InfinityFree before upload.
6. Ensure `application/cache/`, `application/cache/sessions/`, and upload asset folders are writable where the host permits it.
7. Test both URL styles:
   - `https://warehouse.freedev.app/index.php/login`
   - `https://warehouse.freedev.app/login` if `.htaccess` rewrite works.
8. Test login, dashboard, CRUD pages, stock transactions, and all three PDF downloads.

## Production Configuration Checklist

- `application/config/config.php` has production `base_url`.
- `application/config/config.php` keeps `index_page` as `index.php`.
- `application/config/database.php` has InfinityFree host, user, and database.
- `application/config/database.php` has `db_debug` disabled.
- `vendor/autoload.php` exists and loads Composer packages.
- Dompdf class is available through Composer autoload.
- PhpSpreadsheet class is available through Composer autoload.
- Public login-details text files are removed.
- No actual database password is stored in views, JavaScript, logs, or public assets.
