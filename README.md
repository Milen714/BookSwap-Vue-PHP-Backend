# BookSwap

BookSwap is a full-stack book exchange app where users can list books, browse the catalog, request swaps, pay shipping, and chat in real time.

## Features

- Real-time chat powered by the WebSocket server
- Admin dashboard for managing users and platform activity
- Swap token economy for book exchanges
- Password reset flow with email notifications
- Email notifications for swaps and account actions
- Stripe integration for shipping payments

## Tech stack

- Frontend: Vue 3, Vite, Pinia, Vue Router, Tailwind CSS
- Backend: PHP 8, custom MVC, PDO, JWT auth, role middleware
- Realtime: Node.js WebSocket server, Redis pub/sub
- Integrations: Stripe, Google Books API, PHPMailer
- Database: MySQL

## Quick setup

1. Start the backend stack:

```bash
cd Backend
docker compose up
```

2. Import the database dump:

- Open PHPMyAdmin at http://localhost:8080
- Log in with root / secret123
- Import [Backend/DataBaseDump.sql](Backend/DataBaseDump.sql) into the developmentdb database

3. Start the frontend:

```bash
cd Frontend
npm install
npm run dev
```

## Test accounts

- Admin: test@admin.mail / 1234
- User: test@user.mail / 1234

## Run services

- Frontend: http://localhost:5173
- PHP API: http://localhost:8000
- WebSocket server: ws://localhost:8081
- PHPMyAdmin: http://localhost:8080
