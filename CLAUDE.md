# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Development
php artisan serve          # start local server
npm run dev                # start Vite dev server (run alongside artisan serve)

# Build
npm run build              # compile assets for production

# Database
php artisan migrate        # run migrations
php artisan migrate:fresh --seed  # reset and reseed

# Testing
php artisan test                          # run all tests
php artisan test --filter TestClassName   # run single test class
php artisan test tests/Feature/SomeTest.php  # run single file

# Code style
./vendor/bin/pint          # fix PHP code style (Laravel Pint)
```

## Architecture

### Pure Filament Admin Panel
The entire UI is Filament v3. `routes/web.php` contains only a fallback redirect to `filament()->getUrl()`. There are no custom controllers or Blade views outside Filament.

Panel registration: `app/Providers/Filament/AdminPanelProvider.php`
- SPA mode enabled
- `FilamentFullCalendarPlugin` powers the calendar view
- Resources, Pages, and Widgets are auto-discovered from their respective directories

### Multi-Tenancy (Teams)
The app uses Filament's built-in multi-tenancy with `Team` as the tenant model.

- `User` implements `FilamentUser` and `HasTenants`
- Every data query **must** scope to the current tenant: `filament()->getTenant()->id`
- `CalendarWidget` and `ExpensesByTypeChart` are the reference implementations for scoped queries
- Pivot models (`UserTeam`, `ExpenseUser`) live in `app/Models/Pivot/`

### Data Models
| Model | Key details |
|-------|-------------|
| `Location` | Travel events with `from`/`to` datetime, `LocationType` enum, `google_map_link`, `is_visited` |
| `Expenses` | Amount stored as **integer cents** (÷100 on read, ×100 on write via `Attribute` cast) |
| `Team` | Tenant — owns Locations and Expenses via `HasMany` |
| `User` | Belongs to many Teams; belongs to many Expenses |

### Enums
- `App\Enums\LocationType` — implements `HasColor` (returns Filament Color arrays) and `HasLabel`
- `App\Enums\ExpensesType` — same contracts; color values are Filament semantic strings (`'danger'`, `'success'`, etc.) unlike `LocationType` which uses `Color::*` objects

### Filament Resources
Resources in `app/Filament/Resources/`:
- `LocationResource` — exposes `getFormSchema()` as a static method so `CalendarWidget` can reuse it
- `ExpensesResource` — includes `ExpensesByTypeChart` widget in its list page
- `UserResource` — admin user management with `ExpensesRelationManager`

### Global Filament Config (AppServiceProvider)
- All `Section` components default to `columns(2)->compact()`
- All `RichEditor` components default to `columnSpanFull()`
- Custom colors registered: `purple`, `indigo`

### Observers
`LocationObserver` sets `team_id` on creating — but this is now handled explicitly in resource mutations via `filament()->getTenant()->id`. The observer path is less reliable in Filament context; prefer explicit tenant assignment in `mutateFormDataUsing`.
