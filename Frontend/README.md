# Frontend

This folder contains the Vue 3 single-page app.

## Structure

- `src/Views/` - page-level screens
- `src/components/atoms/` - small reusable UI pieces
- `src/components/molecules/` - simple composed components
- `src/components/organisms/` - larger feature components
- `src/stores/` - Pinia stores for app state and API calls
- `src/Router/` - route definitions
- `src/composables/` - shared logic hooks
- `src/utils/` - helpers and axios setup

## Data flow

- Views render pages and compose feature components
- Components call store actions or composables
- Stores handle API requests and state updates
- UI reacts to store state changes automatically

The app is organized by feature, so data moves from route → view → store → API, then back into the UI through reactive state.

## Component structure

- Atom components handle basic inputs, buttons, and icons
- Molecules combine small UI pieces into reusable blocks
- Organisms assemble full sections like navbars, forms, and cards

## Setup

```bash
npm install
npm run dev
```

Set `VITE_API_BASE_URL` in `.env` to point at the backend.
