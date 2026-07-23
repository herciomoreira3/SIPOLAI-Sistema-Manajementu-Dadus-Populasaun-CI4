# SIPOLAI — Sistema Manajementu Dadus Populasaun (CI4)

Sistem ne'e halo gestao ba dadus populasaun iha base iha CodeIgniter 4. Projetu ne'e bele trata familia, moris-an, karta, inventariu no relatoriu.

🌐 Demo Online

https://sipolai-sistema-manajementu-dadus.onrender.com/

Akses admin lokal:

- Password admin lokal agora tenki rai iha `.env` uza `SIPOLAI_ADMIN_PASSWORD=...` antes run migration reset admin.
- Labele rai password demo/default iha repo. Troka password admin husi UI depois login primeira.

⚠️ Rekordu: Konto demo maka hatama ba test no demonstra. Hikas seguru troka pasword bainhira iha produsaun.

🧾 Fitur Prinispal

- 🔒 Autentikasaun no kontrolu sesaun (login/admin)
- 🏘️ Gerensia `Aldeia` no estruktura komunidade
- 👪 Gerensia `Familia` no membru familia
- 🪪 Registro karta (karta sai / karta tama)
- 📦 Inventariu no rastreio `Kargu`
- 📝 Formuláriu pedidu no tipu pedidu (`Pedidu`, `TipuPedidu`)
- 📊 Dadus demografiku: populasaun, profissau, relijiaun, literatura
- 🔁 Migrations no seeders ba konfigurasaun inicial dobdos
- 📁 Upload no guarda dokumentu/fotu iha `public/uploads/`
- 🧩 Arquitetura modular CI4 — fasil pro dezenvolve no modifica

🗂️ Estrutura Projetu (rezumu)

- `app/` — kódigu aplikasaun (Controllers, Models, Config)
- `public/` — entrada `index.php` no assets
- `database/` — migrations no seeds (setup DB)
- `writable/` — cache, logs, uploads
- `tests/` — testu unitariu no exemplos

Hakat ida hotu ba detalhi, buka pasta `app/Models` no `app/Controllers`.

💾 Database

Projetu mak suporta MySQL/MariaDB no kompatível ho TiDB (user hatudu ho TiDB). Ajusta `app/Config/Database.php` to'o datos koneksaun ba TiDB.

Exemplu variables ambiente (.env ou config):

DB.default.hostname = your-tidb-host
DB.default.database = your_database
DB.default.username = your_user
DB.default.password = your_password
DB.default.DBDriver = MySQLi

Run migrasaun no seed ho CodeIgniter (spark):

```
php spark migrate
php spark db:seed YourSeederName
```

🛠️ Deploy

Projetu ne'e mak hetan deploy iha Render.com (see URL). Ba deploy manual:

1. Ajusta environment variables no `baseURL`.
2. Run `composer install` ba instala dependency.
3. Run migrations no seed se hakarak.
4. Assegura pasta `writable/` bele halakon escrita.

Lokál Running

1. Kopia `env` to `.env` no ajusta konfigurasaun.
2. Run `composer install`.
3. Run server dev:

```
php spark serve
```

Abre `http://localhost:8080`.

🤝 Kontribuisaun

Favor buka issue ka envia pull request. Halo testu sira antes hetan merge.

📬 Kontaktu

Kontaktu maintainer repo ba informasaun liu tan.

# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
