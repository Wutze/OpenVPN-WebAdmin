# Offline Asset Release Workflow

This workflow builds and packages all frontend assets for OpenVPN-WebAdmin into a static archive.
The webserver only needs static files and does not need `node` or `npm`.

## What it creates

- `assets-build/release/tools-assets.tar.gz`
- `assets-build/release/tools-assets.tar.gz.sha256`
- `assets-build/staging/tools/manifest.json`

The archive contains the `/tools` tree expected by the application, including current `/int/...` paths:

- `tools/AdminLTE-3.2.0/...`
- `tools/bootstrap5/...`
- `tools/bootstrap-table/...`
- `tools/jquery/jquery-min.js`
- `tools/int/bootstrap-icons/...`
- `tools/int/flatpickr/...`
- `tools/int/bootstrap-datepicker/...`

## Build (outside production webserver)

```bash
cd assets-build
npm ci
bash scripts/build-tools-assets.sh
```

`package-lock.json` should be committed after first build to keep dependency resolution reproducible.

## Deploy (on webserver, no npm required)

```bash
bash assets-build/scripts/deploy-tools-assets.sh /srv/www/openvpn-webadmin/public
```

Optional custom archive and sha256 path:

```bash
bash assets-build/scripts/deploy-tools-assets.sh \
  /srv/www/openvpn-webadmin/public \
  /tmp/tools-assets.tar.gz \
  /tmp/tools-assets.tar.gz.sha256
```

## App config

Set in `config/config.php`:

```php
'sitetools' => '/tools',
```

## Security notes

- Build and dependency resolution happen only in the trusted build environment.
- Webserver receives static artifacts only.
- Integrity check uses SHA256 file if present.
