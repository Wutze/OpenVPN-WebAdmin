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

'''code
bash <(wget -qO- 'https://raw.githubusercontent.com/Wutze/OpenVPN-WebAdmin/master/install.sh')
'''

Alles andere wird dann automatisch abgefragt und installiert.
