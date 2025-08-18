# lua.sh - Scalable & Open-Source URL Shortener

**lua.sh** is a next-generation, scalable, and open-source URL shortener built with the modern stack of Laravel, Vue 3, and Inertia.js. Designed to be fast, flexible, and developer-friendly, **lua.sh** empowers you to create and manage your own professional URL shortening service with ease.

Whether you're an individual looking for branded, trackable links or a business aiming to scale with advanced analytics and custom domains, **lua.sh** is your perfect solution. It’s lightweight, fully customizable, and ready to grow with your needs.

## 🌟 Features

- 🌐 **Shorten URLs**: Quickly shorten long URLs with an intuitive interface.
- 📊 **Detailed Analytics**: Track clicks, referers, devices, geolocation, and more with a comprehensive dashboard.
- ⚡ **Custom Domains**: Add and manage custom domains to create branded URLs.
- 🔗 **Custom URL Slugs**: Personalize your links with memorable, custom slugs.
- 📱 **Responsive Design**: Enjoy a mobile-friendly front end built with Vue 3 and TailwindCSS.
- 🔒 **Authentication & Authorization**: Protect the service with secure authentication and manage permissions for team-based usage.
- 💡 **Link Expiration & Management**: Set expiration dates for links and manage them effortlessly.
- 🔐 **Secure by Design**: Includes built-in CSRF protection, rate-limiting, and HTTPS support.

---

## 🛠️ Tech Stack

### **Backend**
- [Laravel](https://laravel.com/): A robust PHP framework designed for high-performance, scalable applications.
- [MySQL](https://www.mysql.com/): Reliable relational database for storing and managing data.

### **Frontend**
- [Vue 3](https://vuejs.org/): A progressive, reactive JavaScript framework for creating modern interfaces.
- [Inertia.js](https://inertiajs.com/): Seamlessly integrates server-side rendering with single-page app features.
- [TailwindCSS](https://tailwindcss.com/): Utility-first CSS framework for designing clean and responsive UIs.

### **Middleware**
- [Inertia.js](https://inertiajs.com/): Combines Laravel and Vue into a smooth SPA-like experience.

---
### Images
![Analytics](.github/screenshot/analytics.png)
![Events](.github/screenshot/events.png)

**Dark Mode is also available**
![Dark Mode](.github/screenshot/events-dark.png)

## 🌟 Premium Domains - Cloud Only
**lua.sh** provide you access to an array of sleek and memorable domains for your shortened URLs:

- 🔗 **lua.sh**: The default, clean, and professional option.
- 🔗 **git.now**: Perfect for developers and teams sharing repositories.
- 🔗 **cal.now**: Ideal for sharing meeting links or scheduling tools.
- 🔗 **fig.now**: A unique and versatile option for creatives.
- 🔗 **spoti.now**: Great for sharing music playlists and podcasts.

For self-hosting or local development, please check the [Self Hosting](https://developers.lua.sh/self-hosting) or [Local Development](https://developers.lua.sh/local-development) guides.

## Contributors ✨

<!-- ALL-CONTRIBUTORS-LIST:START - Do not remove or modify this section -->
<!-- prettier-ignore-start -->
<!-- markdownlint-disable -->
<table>
  <tbody>
    <tr>
      <td align="center" valign="top" width="14.28%"><a href="https://github.com/paulocastellano"><img src="https://avatars.githubusercontent.com/u/265964?v=4?s=100" width="100px;" alt="Paulo Castellano"/><br /><sub><b>Paulo Castellano</b></sub></a><br /><a href="https://github.com/luadotsh/lua/commits?author=paulocastellano" title="Code">💻</a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://conferencias.dev/"><img src="https://avatars.githubusercontent.com/u/6404401?v=4?s=100" width="100px;" alt="Victor Gazotti"/><br /><sub><b>Victor Gazotti</b></sub></a><br /><a href="https://github.com/luadotsh/lua/commits?author=Arkanius" title="Code">💻</a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://muhammadsaim.com"><img src="https://avatars.githubusercontent.com/u/19898499?v=4?s=100" width="100px;" alt="Muhammad Saim"/><br /><sub><b>Muhammad Saim</b></sub></a><br /><a href="https://github.com/luadotsh/lua/commits?author=MuhammadSaim" title="Code">💻</a></td>
      <td align="center" valign="top" width="14.28%"><a href="https://promium.pro"><img src="https://avatars.githubusercontent.com/u/6384282?v=4?s=100" width="100px;" alt="Ibon Azkoitia"/><br /><sub><b>Ibon Azkoitia</b></sub></a><br /><a href="https://github.com/luadotsh/lua/commits?author=IbonAzkoitia" title="Code">💻</a></td>
    </tr>
  </tbody>
  <tfoot>
    <tr>
      <td align="center" size="13px" colspan="7">
        <img src="https://raw.githubusercontent.com/all-contributors/all-contributors-cli/1b8533af435da9854653492b1327a23a4dbd0a10/assets/logo-small.svg">
          <a href="https://all-contributors.js.org/docs/en/bot/usage">Add your contributions</a>
        </img>
      </td>
    </tr>
  </tfoot>
</table>

<!-- markdownlint-restore -->
<!-- prettier-ignore-end -->

<!-- ALL-CONTRIBUTORS-LIST:END -->

## 💎 Sponsors
A big thanks to our sponsors and partners who make Lua possible. If you're interested in sponsoring Lua and supporting the project, please check out our profile on [GitHub sponsors](https://github.com/sponsors/luadotsh) ❤️

[mercantive.com](https://mercantive.com) • [changelogfy.com](https://changelogfy.com)


## ⚡ Quick Start

| Step | Command |
|------|--------|
| Clone the repo | `git clone https://github.com/wmhchathuranga/lua.sh.git && cd lua.sh` |
| Copy env file | `cp .env.example .env` and update the .env |
| Install PHP deps | `composer install` *(or Sail Composer)* |
| Start Docker | `docker compose -f docker-compose.yml up -d --build` |
| Laravel setup | `./vendor/bin/sail artisan key:generate && ./vendor/bin/sail artisan migrate --seed && ./vendor/bin/sail artisan storage:link` |
| Frontend setup | `./vendor/bin/sail npm ci && ./vendor/bin/sail npm i -D tailwindcss@^3 postcss@^8 autoprefixer@^10 && ./vendor/bin/sail npm run dev` |
| Start Analytics | `./vendor/bin/sail artisan queue:work` |
| Access app | [http://localhost](http://localhost) |

---

## 🚀 Installation

Follow these steps for a full local development environment:

### **Prerequisites**
- [Docker](https://www.docker.com/products/docker-desktop)  
- Docker Compose plugin  
- Git  
- Node.js & npm *(optional if using Sail’s Node image)*  
- Composer *(optional if using Sail’s Composer image)*  

---

### **1️⃣ Clone & Configure**
```bash
git clone https://github.com/wmhchathuranga/lua.sh.git lua.sh
cd lua.sh

# Copy environment
cp .env.example .env
```
And update the .env
---

### **2️⃣ Install PHP Dependencies**
```bash
composer install
```

---

### **3️⃣ Start the Docker Stack**
```bash
docker compose -f docker-compose.yml up -d --build
```

Verify services:
```bash
./vendor/bin/sail ps
```

---

### **4️⃣ Laravel Setup**
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan storage:link
```

---

### **5️⃣ Install Frontend**
```bash
./vendor/bin/sail npm ci

# Ensure compatible TailwindCSS version
./vendor/bin/sail npm i -D tailwindcss@^3 postcss@^8 autoprefixer@^10

# Run Vite dev server
./vendor/bin/sail npm run dev
```

---

### **✅ Access Your App**
- **Application:** [http://localhost](http://localhost)  
- **MailHog (emails):** [http://localhost:8025](http://localhost:8025)

---

## 🔹 Useful Commands

| Task                        | Command                                       |
|-----------------------------|-----------------------------------------------|
| Stop containers             | `./vendor/bin/sail down`                      |
| Rebuild without cache       | `./vendor/bin/sail build --no-cache`          |
| Laravel logs                | `./vendor/bin/sail logs -f laravel.test`      |
| Queue worker (dev)          | `./vendor/bin/sail artisan queue:work`        |
| Fresh DB migration & seed   | `./vendor/bin/sail artisan migrate:fresh --seed` |
| Clear caches                | `./vendor/bin/sail artisan config:clear && ./vendor/bin/sail artisan cache:clear` |

---
```
1. cp .env.example .env
2. composer i
3. docker compose -f docker-compose.yml up -d --build
4. ./vendor/bin/sail ps
5. ./vendor/bin/sail artisan key:generate
6. ./vendor/bin/sail artisan migrate --seed
7. ./vendor/bin/sail artisan storage:link
8. ./vendor/bin/sail npm ci
9. ./vendor/bin/sail npm i -D tailwindcss@^3 postcss@^8 autoprefixer@^10
10. ./vendor/bin/sail npm run dev
```