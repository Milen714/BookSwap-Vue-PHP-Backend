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
