# Backend

This folder contains the PHP API, view rendering, and supporting services for BookSwap.

## Structure

- `app/public/` - entry point, route dispatcher, legacy page routes, and public assets
- `app/src/Controllers/` - HTTP request handlers
- `app/src/Services/` - business logic
- `app/src/Repositories/` - database access
- `app/src/Models/` - domain models
- `app/src/Middleware/` - auth and role checks
- `websocket-server/` - Node.js WebSocket server for chat

## API flow

The app uses a custom FastRoute dispatcher in `app/public/index.php`.

Request flow:

1. Route is matched in `index.php`
2. Controller method is resolved dynamically
3. `RoleMiddleware` checks access before execution
4. Controllers call services and repositories
5. JSON or view responses are returned depending on the route

## REST API (final)

Base URL in local development is typically `http://localhost`.

- Content type: `application/json`
- Auth: JWT via `Authorization: Bearer <token>` for protected routes
- CORS preflight: `OPTIONS` is supported

### Auth

- `POST /login`
- `POST /logout`
- `POST /signUp`
- `GET /getLoggedInUser`
- `POST /forgot-password`
- `GET /reset-password`
- `POST /reset-password`

### Theme / Preferences

- `PUT /setTheme`

### Books

- `POST /fetchBookPreview`
- `POST /addBook`
- `GET /scanBook/{isbn}`
- `GET /getAllBooks`
- `GET /getAllGenres`
- `GET /getBookDetails`
- `GET /getUserBooks/{userId}`

### Book Requests

- `POST /createBookRequest`
- `PUT /updateRequest`
- `GET /getBookSwapStatusses`
- `GET /getBookRequestById`
- `GET /getMyBookRequests`
- `GET /getMyBookListings`

### Checkout

- `GET /create-checkout-session`
- `POST /checkout-status`

### User / Profile

- `GET /getProfileAddress/{id}`
- `GET /getUserInfo`
- `GET /getUserTokens`
- `PUT /updateProfile`
- `PUT /changePassword`
- `PUT /updateAddress`
- `GET /getUser/{userId}`

### Admin

- `GET /getAllUsers`
- `GET /getAdminAnalytics`
- `PUT /toggleUserStatus`

### Chat

- `GET /getChatMessages`
- `POST /sendDirectMessage`
- `GET /getChatPartners`

## REST notes

- Update-style operations are currently implemented with `PUT` (`/setTheme`, `/updateRequest`, `/updateProfile`, `/changePassword`, `/updateAddress`, `/toggleUserStatus`).
- Create-style operations are implemented with `POST`.
- Read-style operations are implemented with `GET`.
- There are currently no explicit `DELETE` endpoints in the dispatcher.

## Auth and permissions

- JWT is used for stateless authentication
- Protected routes validate the token server-side
- Role checks are enforced with PHP attributes and `RoleMiddleware`
- Controllers can require user or admin access without duplicating auth logic

## WebSocket server

The chat system runs through `websocket-server/server.js`.

- PHP sends messages through the backend API
- Redis pub/sub relays chat events
- The Node server broadcasts updates to connected clients

## Notes

- Keep business logic in services, not controllers
- Keep SQL in repositories
- Keep route definitions in `app/public/index.php`
