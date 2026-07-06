# Profess0rPay — Open Source Self-Hosted Payment Automation Platform

<p align="center">
  <a href="LICENSE">
    <img src="https://img.shields.io/badge/License-AGPL--3.0-blue.svg?style=for-the-badge&logo=gnu&logoColor=white" alt="AGPL-3.0 License">
  </a>
</p>

**Goal:** Deliver a stable, easy-to-install, white-label self-hosted payment gateway for individuals and small businesses. Advanced features will be developed incrementally in future releases based on community feedback.

Profess0rPay is an open-source payment automation system (AGPL-3.0) — a self-hosted, plugin-based platform that unifies payment gateways, wallets, APIs, and SMS-based verification into one system. It allows anyone to easily launch their own customized payment gateway within 2 minutes.

---

## 📱 Official Android Companion App
Manage your transactions, notifications, and gateways directly from your smartphone!
**Download the official App:** [ From Google Play Store](https://play.google.com/store/apps/details?id=com.qubeplug.billpax_tools)

---

## ✨ Features
- **2-Minute Installer**: Easy graphical setup wizard right out of the box.
- **White-Label Branding**: Full control to change your brand name, logo, favicon, and primary colors directly from the Admin Panel.
- **Payment Link Generation**: Easily create and share beautiful payment links.
- **REST API**: Connect your website, apps, and external systems to the gateway effortlessly.
- **Gateway Management**: Upload custom QR codes for manual and MFS (Mobile Financial Services) payments.

---

## ⚙️ System Requirements
To run Profess0rPay, your server (cPanel / VPS) must meet the following requirements:
- **PHP**: Version 8.1 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **PHP Extensions**:
  - `PDO` and `pdo_mysql` (for database connections)
  - `cURL` (for external API requests and Webhooks)
  - `mbstring` (for string handling)
  - `OpenSSL` (for secure encryption)
  - `JSON` (for data formatting)

---

## 🚀 Installation Guide

Installing Profess0rPay is incredibly simple and takes less than 2 minutes.

1. **Download & Upload**: Download the latest release from the repository and upload the files to your server's public directory (e.g., `public_html` on cPanel or `/var/www/html` on a VPS).
2. **Database Setup**: Create a new, empty MySQL database and a corresponding database user with full privileges.
3. **Run the Installer**: Visit your domain in a web browser (e.g., `https://yourdomain.com/`). The automatic 2-Minute Installer will greet you.
4. **Configuration**: 
   - Follow the on-screen instructions.
   - Enter your newly created Database Name, Username, and Password.
   - Set up your primary Admin Account credentials.
5. **Finish**: Once completed, the system will automatically configure your environment and log you in!

---

## 🙏 Credits & Acknowledgements

This project was built to expand upon open-source payment automation capabilities. 
**Special thanks and credit** goes to the original developers of [PipraPay](https://github.com/PipraPay/PipraPay). Profess0rPay is a community-driven fork and continuation of the PipraPay ecosystem, aimed at adding stability, security patches, and extended administrative controls.

---

## 📅 Release Strategy

- **v1.0** — Stable (Current): Installer, White-label Branding, Admin Panel, Payment Links, REST API, UI Mobile Fixes, Security Enhancements.
- **v1.1** — Planned: WooCommerce Plugin / PHP SDK.
- **v1.2** — Planned: Docker Support & GitHub Actions (CI).
- **v2.0** — Planned: Merchant System, New Gateways, Better UI.

## 🤝 Contributing
We welcome contributions! Please see our [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to submit pull requests, report issues, and suggest features.

## 🔒 Security
If you discover a security vulnerability, please review our [SECURITY.md](SECURITY.md) guidelines for responsible disclosure.

## 📄 License
This project is licensed under the **GNU Affero General Public License v3.0 (AGPL-3.0)**. See the [LICENSE](LICENSE) file for more details.
