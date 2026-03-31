# BookSwap - Book Exchange Platform

BookSwap is a full-stack book exchange platform built with **Vue.js 3** frontend and **PHP 8** backend. Users can list books, browse titles, request swaps with other readers, and communicate in real-time through WebSocket-powered chat—all facilitated through a token-based economy system.

## TEST ACCOUNTS

| Account | Email           | Password |
| ------- | --------------- | -------- |
| Admin   | test@admin.mail | 1234     |
| User    | test@user.mail  | 1234     |

## Quick Start

```bash
# Start all services
docker compose up

# Import database schema
# Open PHPMyAdmin at localhost:8080 (root:secret123)
# Import Backend/DataBaseDump.sql into 'developmentdb'
```

---

## Features

- **Book Discovery**: Search and browse books by title, author, or genre
- **Smart Book Listing**: Add books by ISBN with automatic metadata from Google Books API
- **Real-Time Chat**: WebSocket-powered direct messaging between users using Redis pub/sub
- **Swap Requests**: Request books with automatic shipping cost calculation
- **Stripe Payments**: Secure checkout for shipping fees
- **Token Economy**: Users earn/spend tokens for book exchanges
- **Secure Authentication**: JWT-based auth with role-based access control
- **Barcode Scanner**: Mobile-friendly book scanning (HTTPS only)
- **Theme Support**: Light/dark mode with persistent cookies
- **Email Notifications**: Automated swap and password reset emails

---

## Architecture

### Full-Stack Tech Stack

**Frontend**

- Vue.js 3 (Composition API with `<script setup>`)
- Vue Router for navigation
- Pinia for state management
- TailwindCSS + PrimeIcons for styling
- Vite dev server with hot reload

**Backend**

- PHP 8 with custom MVC framework
- MySQL database with PDO queries
- Redis for real-time pub/sub messaging
- WebSocket server (Node.js) for live chat
- Stripe & Google Books API integration

### Project Structure

```
BookSwapVue/
├── Frontend/                    # Vue.js SPA
│   ├── src/
│   │   ├── components/         # Reusable UI components
│   │   │   ├── atoms/         # Button, inputs, etc.
│   │   │   ├── molecules/     # Spinner, cards, etc.
│   │   │   └── organisms/     # Complex components (ProfileHeader, etc.)
│   │   ├── stores/            # Pinia state management
│   │   │   ├── auth.js        # Authentication state
│   │   │   ├── chat.js        # Chat & WebSocket state
│   │   │   ├── books.js       # Books catalog state
│   │   │   └── profile.js     # User profile state
│   │   ├── Router/            # Vue Router setup
│   │   └── Views/             # Page components
│   └── vite.config.js
│
└── Backend/                     # PHP API & WebSocket server
    ├── app/
    │   ├── src/
    │   │   ├── Controllers/    # Request handlers
    │   │   ├── Services/       # Business logic
    │   │   ├── Repositories/   # Data access
    │   │   ├── Models/         # Domain models
    │   │   └── Middleware/     # JWT & role validation
    │   ├── public/             # Entry point
    │   └── Views/              # Template files
    ├── websocket-server/       # Node.js WebSocket server
    ├── docker-compose.yml
    └── PHP.Dockerfile
```

---

## Key Technical Implementations

### 1. Real-Time Chat with WebSocket + Redis

**Architecture**: Vue.js client → WebSocket server (Node.js) → Redis pub/sub → All connected clients

**How It Works**:

1. User sends message via `ChatForm.vue`
2. Message posted to PHP API endpoint `/sendDirectMessage`
3. PHP service publishes message to Redis channel: `chat-channel`
4. WebSocket server continuously subscribes to Redis pub/sub
5. On message received, WebSocket broadcasts to all connected clients
6. Client store (`chatStore`) receives update and reactively updates UI

**WebSocket Server** (`Backend/websocket-server/server.js`):

```javascript
const redis = new Redis({ host: "redis", port: 6379 });
redis.subscribe("chat-channel", (err, count) => {
  if (!err) console.log(`Subscribed to ${count} channels`);
});

redis.on("message", (channel, message) => {
  const data = JSON.parse(message);
  // Broadcast to all connected WebSocket clients
  broadcastToClients(data);
});
```

**Chat Store** (`Frontend/src/stores/chat.js`):

- Maintains message history for each conversation
- Handles WebSocket lifecycle (connect, disconnect, reconnect)
- Exposes reactive `messages` and `chatPartners`
- Integrates JWT token for secure WebSocket upgrade

### 2. Authentication & Authorization

**JWT Implementation**:

- Generated on login with 24-hour expiration
- Stored in browser `localStorage`
- Automatically attached to all API requests via axios interceptor
- Validated server-side via `JWTMiddleware` on protected routes

**Role-Based Access Control** (PHP Attributes):

```php
#[RequireRole([UserRole::ADMIN])]
public function dashboard() { }

#[RequireRole([UserRole::USER, UserRole::ADMIN])]
public function viewSwapRequests() { }
```

### 3. State Management with Pinia

Organized by domain for clean separation of concerns:

| Store        | Purpose                                   |
| ------------ | ----------------------------------------- |
| `auth.js`    | JWT token, logged-in user, login state    |
| `profile.js` | Viewed user profile data and books        |
| `books.js`   | Book catalog, search, filters, pagination |
| `chat.js`    | Messages, WebSocket connection, partners  |

Each store handles API calls, mutations, and computed properties. Vue components stay thin and focused on rendering.

### 4. Repository & Service Pattern

**Repository Layer** (`Backend/app/src/Repositories/`):

- Encapsulates all database queries
- All queries use prepared statements (prevents SQL injection)
- Example: `BookRepository::getBooksByGenre()`

**Service Layer** (`Backend/app/src/Services/`):

- Implements business logic
- Orchestrates multiple repositories
- Handles external API calls (Google Books, Stripe, PostNL)
- Example: `BookRequestService::createRequest()` handles validation, repository calls, and email notifications

### 5. Payment Flow with Stripe

**Stripe Checkout Integration**:

1. User initiates swap → POST `/createCheckoutSession` with request ID
2. PHP creates Stripe session with shipping cost from `MockPostNLService`
3. Frontend redirects to Stripe embedded checkout
4. After payment → frontend calls `/checkoutStatus` with session ID
5. PHP verifies payment with Stripe API
6. On success: request marked complete, tokens awarded, email sent

**Shipping Calculation** (`MockPostNLService.php`):

- Estimates book weight from page count
- Uses average weight for missing data
- Production version integrates with real PostNL API

### 6. Email Notifications

**Mailer Service** (`Backend/app/src/Services/MailService.php`):

- Uses PHPMailer for SMTP delivery
- Customizable templates for different notification types
- Sends for: swap requests, approvals, rejections, password resets

---

## API Endpoints (Key Routes)

| Endpoint                 | Method | Description                                     |
| ------------------------ | ------ | ----------------------------------------------- |
| `/login`                 | POST   | User authentication, returns JWT                |
| `/getLoggedInUser`       | GET    | Current authenticated user from token           |
| `/getUser/{id}`          | GET    | Public user profile info                        |
| `/getUserBooks/{id}`     | GET    | All books posted by user                        |
| `/getAllBooks`           | GET    | Paginated book catalog with filters             |
| `/fetchBookPreview`      | POST   | Google Books API lookup by ISBN                 |
| `/createBookRequest`     | POST   | Initiate book swap request                      |
| `/updateRequest`         | POST   | Update request status (accept/decline/complete) |
| `/getChatMessages`       | GET    | Message history between two users               |
| `/sendDirectMessage`     | POST   | Send message (triggers Redis broadcast)         |
| `/createCheckoutSession` | POST   | Create Stripe checkout session                  |
| `/checkoutStatus`        | POST   | Verify payment completion                       |

---

## Security Implementation

### SQL Injection Prevention

All database queries use parameterized statements:

```php
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
$stmt->bindParam(':email', $email);
$stmt->execute();
```

### XSS Prevention

All user-generated content is escaped before output:

```php
echo htmlspecialchars($book->title, ENT_QUOTES, 'UTF-8');
```

### Password Security

- Bcrypt hashing for storage (PASSWORD_BCRYPT)
- Constant-time verification to prevent timing attacks

```php
$user->password_hash = password_hash($password, PASSWORD_BCRYPT);
if (password_verify($inputPassword, $user->password_hash)) { }
```

### API Security

- HTTPOnly cookies for session data
- JWT tokens for stateless authentication
- CORS configured for trusted origins
- Password reset tokens expire after 1 hour

### Data Privacy

- Environment variables for API keys and credentials
- Sensitive config files excluded from version control
- Data encrypted in transit with HTTPS

---

## WCAG Accessibility & GDPR

### Accessibility

- Semantic HTML with ARIA attributes (`aria-expanded`, `aria-hidden`, `aria-labelledby`)
- Screen reader support via Tailwind's `sr-only` class
- Color contrast meets WCAG AA standards
- Mobile-first responsive design
- Full keyboard navigation support

### GDPR Compliance

- Minimal data collection (name, email, address for shipping only)
- Passwords stored as hashes only
- Secure password reset with 1-hour token expiry
- Clear notification opt-in mechanisms
- No unnecessary tracking or analytics

---

## Development

### Frontend

```bash
cd Frontend
npm install
npm run dev
```

Runs on `http://localhost:5173` with Vite hot module reload (HMR)

### Backend Services

```bash
cd Backend
docker compose up
```

Services:

- PHP API: `http://localhost:8000`
- PHPMyAdmin: `http://localhost:8080` (root : secret123)
- WebSocket Server: `ws://localhost:8081`
- Redis: `localhost:6379`
- MySQL: `localhost:3306`

### Useful Commands

```bash
# View service logs
docker compose logs -f php
docker compose logs -f websocket

# Access MySQL CLI
docker compose exec mysql mysql -u root -psecret123 developmentdb

# Rebuild containers
docker compose up --build

# Stop containers
docker compose down
```

---

## Next Steps / Roadmap

- [ ] Admin Dashboard with platform metrics (total swaps, active users, etc.)
- [ ] In-person meetup feature for location-based exchanges
- [ ] User ratings & reviews system
- [ ] Book wishlist functionality
- [ ] Advanced search with filters (author, year, language)
- [ ] Social features (followers, book clubs)
- [ ] Mobile app (React Native)

---

## Technical Notes

- Barcode scanner requires HTTPS (demo at https://bookswap.art/)
- Stripe test mode (no real charges)
- Email notifications use demo Gmail account (GardenGroup@gmail.com)
- MySQL automatically initializes on first Docker startup
- Redis persists messages for active conversations
- WebSocket server restarts on code changes

---
