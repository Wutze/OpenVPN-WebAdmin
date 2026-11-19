# OpenVPN-WebAdmin
## Release BETA 2.0.0

**A web-based administration interface for OpenVPN servers.**

OpenVPN-WebAdmin is an open-source PHP application for managing and administering an OpenVPN server through a web browser.

The project aims to provide a clear, structured and easy-to-use administration interface without requiring administrators to perform every routine task directly on the command line.

The application is designed particularly for self-hosted environments, private networks and smaller infrastructures where administrators want to retain full control over their OpenVPN installation.

---

## Project Goals

OpenVPN-WebAdmin is built around a few central principles:

- **Self-hosted:** The application runs on your own infrastructure.
- **Control:** OpenVPN configuration and server integration remain under the administrator's control.
- **Security:** Administrative functions are separated from the actual OpenVPN server processes.
- **Privacy:** No external services or CDN providers are required for the frontend.
- **Transparency:** Configuration, scripts and application components are kept understandable and maintainable.
- **Independence:** No Docker environment or cloud-based infrastructure is required.

The goal is not to replace OpenVPN, but to provide a convenient administration layer for an existing OpenVPN installation.

## Features

### Web-based administration

OpenVPN-WebAdmin provides a browser-based interface for administrative tasks.

The application includes:

- User administration
- User groups and access levels
- OpenVPN-related user configuration
- Management of client configuration files
- Connection and disconnection integration
- Multilingual interface
- Configurable login themes

The interface is based on AdminLTE 3.2.0 and Bootstrap.

### User and client management

Users are managed centrally through the application's database.

Each user is associated with an individual OpenVPN client identity. The username is used as the Common Name (CN) of the corresponding client certificate.

Each user is assigned a static VPN IP address.

The assignment is handled through the OpenVPN connection scripts. When a client connects, the assigned address is retrieved from the database and supplied to OpenVPN.

This provides a predictable and centrally managed address assignment instead of relying on dynamically allocated client addresses.

### OpenVPN integration

OpenVPN-WebAdmin integrates with an existing OpenVPN server through dedicated connection and disconnection scripts.

The integration uses the OpenVPN hooks:

- `client-connect`
- `client-disconnect`

These scripts communicate with the application's database and handle the associated connection events.

The application and OpenVPN server remain separate components. The web application does not need to run with root privileges simply to administer its users.

Certificate and private-key generation and management are deliberately kept outside the web application. This avoids placing sensitive cryptographic material under the direct control of the web interface.

## Technical Architecture

OpenVPN-WebAdmin uses a conventional server-side PHP architecture.

| Component | Technology |
|---|---|
| Backend | PHP |
| Database | MariaDB / MySQL |
| Webserver | Apache 2 or Nginx |
| VPN service | OpenVPN |
| Frontend framework | AdminLTE 3.2.0 |
| CSS framework | Bootstrap |
| JavaScript | jQuery and additional libraries |
| Installation | Bash |
| Database communication | PHP database interface |

The database can run locally or on a separate database server.

Apache and Nginx are supported as webserver options. The installation can configure an existing webserver or prepare a new installation.

## Frontend and Local Assets

All frontend libraries are provided locally as part of the application.

OpenVPN-WebAdmin does not depend on external CDNs for loading its CSS, JavaScript, fonts or icons.

This has several advantages:

- No CDN-related connections to external third parties.
- No additional browser data transmission to CDN providers.
- No dependency on external CDN availability.
- Full control over library versions.
- Predictable frontend behavior.

The frontend assets are prepared separately from the productive application.

The build process uses Node.js and npm exclusively on the development system. The resulting files are packaged into a TAR.GZ archive and accompanied by a SHA256 checksum.

During installation, the prepared archive is verified and deployed automatically.

Node.js and npm are therefore not required on the productive server when a prepared asset archive is supplied.

See the section *Local Frontend Asset Deployment* in the project documentation for details.

## Installation

OpenVPN-WebAdmin includes an interactive installation script based on `whiptail`.

The setup process guides the administrator through the required configuration.

The installation covers:

1. System and environment checks.
2. Selection of the application source repository and branch.
3. Definition of installation and deployment directories.
4. Configuration of the webserver user and group.
5. Database connection settings.
6. Creation of a local MariaDB database, if required.
7. Creation of the initial administrator account.
8. Preparation and deployment of the application files.
9. Installation of the frontend assets.
10. Generation of the application configuration.
11. Integration with the OpenVPN server.
12. Optional automatic configuration of Apache or Nginx.
13. Setting and checking file permissions.

The installer supports two deployment approaches:

- **Standalone:** OpenVPN-WebAdmin is provided as an independent website.
- **Subdirectory:** The application is made available under a path such as `/openvpnwebadmin`.

An existing database server and webserver can also be used.

### Installation requirements

The installation environment requires:

- A supported Debian-based Linux system with APT package management.
- Root privileges during installation.
- PHP and the required PHP extensions.
- MariaDB or MySQL.
- Apache 2 or Nginx.
- An installed or separately prepared OpenVPN server.
- Git and the required system utilities.

The exact packages required by the installer are determined by the selected installation options.

## Security Considerations

Security is an important design consideration throughout the project.

Some of the underlying principles are:

**Separation of responsibilities**

The PHP application, database and OpenVPN service have distinct responsibilities. The web application does not need unrestricted system privileges for routine administration.

**External certificate management**

Private keys and certificates are not generated or managed by the web application. Their preparation and provisioning remain separate administrative tasks.

**Controlled file permissions**

Application files, configuration files and OpenVPN integration scripts are installed with defined ownership and permissions.

**Local frontend dependencies**

The application does not rely on external CDN providers or third-party frontend delivery services.

**Controlled installation**

The installation process explicitly collects and validates the required configuration parameters before proceeding.

Security also depends on the correct configuration of the underlying Linux system, database, webserver and OpenVPN service.

## Project Structure

The repository separates the application, server-side integration, installation resources and frontend assets.

Important directories include:

| Directory | Purpose |
|---|---|
| `public/` | Public web entry point and frontend resources |
| `src/` | Application source code |
| `html/` | HTML templates and login themes |
| `config/` | Application configuration templates |
| `storage/` | Application configuration, data and runtime files |
| `server/installation/` | Database installation resources |
| `server/scripte/` | OpenVPN connection and disconnection scripts |
| `assets-build/` | Frontend asset build and deployment environment |
| `doc/` | Project documentation |

### Storage and server configuration

The installer creates `storage/conf/{linux,osx,windows}/history`,
`storage/conf/history/server`, and `storage/logs` under the selected deployment directory.
Missing `client.ovpn` and `update-resolv.sh` templates are copied without overwriting existing
storage files. Writable directories are owned by `www-data:www-data` with mode `2770` (setgid);
default ACLs also grant access to a different configured web-server user. Existing files are
made readable and writable for these users and inaccessible to others. Inherited ACLs cannot
override explicitly restrictive creation modes, such as `0600`. The installer verifies access
and fails if the checks do not pass.

When setting up or renewing the VPN server, `server/installation/openvpn-setup-easyrsa.sh`
asks for the deployment directory and links `storage/conf/server/server.conf` to
`/etc/openvpn/server.conf`. An existing file or different link is preserved under a unique
`.bak.*` name. The link directory is `root:www-data` mode `0750`, and the target file is
`root:www-data` mode `0640`: ACLs grant `www-data` and the deployment owner at most read
access. The deployment owner must not be `root`. Linked server
configurations are read-only in the WebAdmin; local non-linked configurations retain
their existing save behavior.

The installation script and supporting shell functions are provided separately.

## Development

OpenVPN-WebAdmin is developed with an emphasis on understandable code and maintainable structures.

The project uses established technologies instead of requiring a large additional runtime environment.

Frontend libraries are managed through npm on the development system. Their versions are controlled through `package.json` and `package-lock.json`.

The prepared frontend files are then included in the application deployment.

The productive system does not require the Node.js development environment.

## License

OpenVPN-WebAdmin is distributed under the terms of the **GNU Affero General Public License v3.0 (AGPL-3.0)**.

See [LICENSE.md](LICENSE.md) for the complete license text.

## Project

**Repository:** [Wutze/OpenVPN-WebAdmin](https://github.com/Wutze/OpenVPN-WebAdmin)

Bug reports, suggestions and contributions are welcome through the project's issue tracker.

---

*OpenVPN-WebAdmin – Administration of your own OpenVPN infrastructure, under your own control.*

















# OpenVPN WebAdmin 2.0.0

You lock your front door. But why do you leave the back entrances open? This is the same with almost all Internet accesses to your IoT, webcams and other devices.

Create and manage your virtual private network via web browser and OpenVPN. This system is a simple and easy method for your private user manager. The system is suitable for families, shared flats or companies that value free software. If you want to become independent from big cloud providers, if you really care about the security of your data without having to reveal the communication to secret services or data collectors, you will find your way with this system.

## Offline assets for `/tools`

If you do not want external CDNs and do not want to run `npm` on the webserver:

1. Build static frontend assets in a trusted build environment:
   `assets-build/scripts/build-tools-assets.sh`
2. Copy `assets-build/release/tools-assets.tar.gz` (+ `.sha256`) to the target server.
3. Deploy static files only:
   `assets-build/scripts/deploy-tools-assets.sh /srv/www/openvpn-webadmin/public`
4. Use local sitetools path in config:
   `'sitetools' => '/tools'`

See `assets-build/README.md` for details.

## Installation

Start with Code:

```code
bash <(wget -qO- 'https://raw.githubusercontent.com/Wutze/OpenVPN-WebAdmin/master/install.sh')
```

Alles andere wird dann automatisch abgefragt und installiert.

## Update (PHP only, keep server config)

Use the update script to refresh application PHP files without touching runtime configuration files.

```bash
sudo ./update.sh
```

The updater:
- copies only `*.php` files from source to deploy target
- keeps `config/config.php` unchanged
- keeps `.env` unchanged
- does not run database migrations
