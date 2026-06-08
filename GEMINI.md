# Project Overview: Vue-Laravel Template

A modern, full-stack CMS-like template utilizing Laravel for the backend API and Nuxt (Vue) for the frontend.

## Tech Stack
- **Backend:** Laravel 13+, PHP 8.3, JWT Auth, Swagger/OpenAPI documentation.
- **Frontend:** Nuxt 4+, Vue 3, Pinia (State Management), TypeScript.
- **Database:** Prepared for multi-language content (Languages, Translations) and Media management.

## Project Structure
- `/backend`: Laravel API application.
- `/frontend`: Nuxt frontend application.
- `/public/media`: Shared storage for media assets.

---

## Frontend Tokenized Design

The frontend uses a dynamic, token-based design system managed through Pinia and CSS Custom Properties.

### Design Tokens
Tokens are defined in `frontend/app/utils/themeConfig.ts` and managed by `useThemeStore` in `frontend/app/store/themeStore.ts`.

#### Core Tokens:
- **Colors:** `primary`, `secondary`, `surface`, `background`, `error`, `outline` (plus `on-*` variants for contrast).
- **Typography:** `font-sans`, `font-serif`, `font-mono`.

### Implementation
- **Injection:** Tokens are injected into `:root` as CSS variables (e.g., `--color-primary`, `--bg-primary`, `--font-main`).
- **Persistence:** Current theme (`light` or `dark`) is stored in a cookie.
- **Usage:** Components should strictly use CSS variables from `main.css` or direct variable references.

---

## LLM-Friendly Guidelines

### General Development
- **Surgical Changes:** Only modify what is necessary for the task.
- **Types First:** Always check or define types in `frontend/app/types/*.ts` before implementation.
- **Consistency:** Follow existing naming conventions (e.g., `camelCase` for JS/TS, `PascalCase` for Components, `snake_case` for PHP).

### Frontend (Nuxt)
- **Design System:** Follow **Material Design 3 (M3)** principles for all components. Utilize the tokenized design system for elevations, spacing, and states (hover, focus, disabled).
- **Stores:** Use Pinia stores located in `app/store/` for global state.
- **Composables:** Use `useApi` for backend requests to ensure consistent auth/header handling.
- **Theming:** **NEVER** hardcode hex colors in Vue components. Use the CSS variables (e.g., `color: var(--color-primary)`).
- **Layouts:** Use the `default` layout for public pages and `dashboard` for authenticated areas.

### Backend (Laravel)
- **Models:** Adhere to the translation-ready model structure (e.g., `Blog` + `BlogTranslation`).
- **API:** Ensure all new endpoints are documented via Swagger annotations if present.
- **Auth:** Use JWT for all protected routes.

### Media & Assets
- Media is managed via the `Media` model and shared `public/media` directory.
- Use the `media.ts` composable for handling uploads and URL generation.
