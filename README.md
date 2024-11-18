# lua.sh - Open Source URL Shortener

**lua.sh** is a modern, open-source URL shortener built with the powerful combination of Laravel, Vue 3, and Inertia.js. It is designed to provide a seamless, customizable, and developer-friendly solution for running your own URL shortening service. Whether you're an individual looking for a branded solution or a business in need of robust link tracking, **lua.sh** has you covered.

## 🌟 Features

- 🌐 **Shorten URLs**: Quickly shorten long URLs with an intuitive interface.
- 📊 **Detailed Analytics**: Track clicks, referrers, devices, geolocation, and more with a comprehensive dashboard.
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

## 🚀 Getting Started

### Requirements
- PHP 8.1 or later
- Composer
- Node.js with npm
- MySQL

### Installation

1. Clone the repository:
```bash
git clone https://github.com/luadotsh/lua.git
cd lua
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Set up environment variables:
```bash
cp .env.example .env
```

**Update your .env file with database and app settings.**

5. Run migrations and seed the database:
```bash
php artisan migrate:fresh --seed
```

6. Run the front-end build:
```bash
npm run dev
```
### 💻 Running with Reverb

```bash
php artisan reverb:start --host="0.0.0.0" --port=8080 --hostname="lua.sh.test"
```
