# Project Overview: Vue-Laravel Template

A modern, full-stack CMS-like template using Laravel for the backend API and Nuxt/Vue for the frontend.

## Tech Stack

- **Backend:** Laravel 13+, PHP 8.3, JWT Auth, Swagger/OpenAPI documentation.
- **Frontend:** Nuxt 4+, Vue 3, Pinia, TypeScript.
- **Database:** Prepared for multi-language content with Languages, Translations, and Media management.

## Project Structure

- `/backend`: Laravel API application.
- `/frontend`: Nuxt frontend application.
- `/public/media`: Shared storage for media assets.

## Frontend Tokenized Design

The frontend uses a dynamic, token-based design system managed through Pinia and CSS custom properties.

### Design Tokens

Tokens are defined in `frontend/app/utils/themeConfig.ts` and managed by `useThemeStore` in `frontend/app/store/themeStore.ts`.

Core tokens include:

- **Colors:** `primary`, `secondary`, `surface`, `background`, `error`, `outline`, plus `on-*` variants for contrast.
- **Typography:** `font-sans`, `font-serif`, `font-mono`.

### Implementation

- **Injection:** Tokens are injected into `:root` as CSS variables such as `--color-primary`, `--bg-primary`, and `--font-main`.
- **Persistence:** The current theme, `light` or `dark`, is stored in a cookie.
- **Usage:** Components should use CSS variables from `main.css` or direct variable references.

## Codex Guidelines

### General Development

- Make surgical changes and modify only what is necessary for the task.
- Check or define types in `frontend/app/types/*.ts` before implementation.
- Follow existing naming conventions: `camelCase` for JS/TS, `PascalCase` for Vue components, and `snake_case` for PHP.
- Preserve unrelated user changes in the working tree.

### Frontend: Nuxt

- Follow Material Design 3 principles for components.
- Use the tokenized design system for elevations, spacing, and states such as hover, focus, and disabled.
- Use Pinia stores in `frontend/app/store/` for global state.
- Use `useApi` for backend requests to keep auth and header handling consistent.
- Never hardcode hex colors in Vue components. Use CSS variables, for example `color: var(--color-primary)`.
- Use the `default` layout for public pages and `dashboard` for authenticated areas.

### Backend: Laravel

- Follow the translation-ready model structure, such as `Blog` and `BlogTranslation`.
- Document new endpoints with Swagger annotations when existing routes/controllers use them.
- Use JWT for protected routes.

### Media & Assets

- Media is managed through the `Media` model and shared `public/media` directory.
- Use `frontend/app/utils/media.ts` for uploads and URL generation helpers.
