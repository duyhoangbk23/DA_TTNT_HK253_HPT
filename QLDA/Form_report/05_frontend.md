# 05. Frontend Review - smartwater-admin

Scope: frontend source only. Views, components, layouts, JavaScript, and CSS.

## Frontend Stack

- Blade templates under `resources/views`.
- Shared layout and partials under `resources/views/layouts` and `resources/views/partials`.
- Reusable Blade components under `resources/views/components`.
- Source JS entry in `resources/js/app.js` is empty in this repository.
- Runtime JS helpers are in `public/js/app.js`.
- Source CSS entry in `resources/css/app.css` imports Tailwind.
- Runtime theme CSS is in `public/css/app.css`.

## Page Hierarchy

- `auth/login.blade.php`
  - Standalone auth page, not wrapped by `layouts.app`.
- `layouts/app.blade.php`
  - Main shell for all authenticated pages.
  - Includes `partials/sidebar`, `partials/navbar`, and `partials/footer`.
  - Provides page title, subtitle, breadcrumb, actions, and content slots.
- Authenticated pages using the layout:
  - `dashboard/index.blade.php`
  - `products/index.blade.php`
  - `categories/index.blade.php`
  - `inventory/index.blade.php`
  - `batch/index.blade.php`
  - `customers/index.blade.php`
  - `customers/show.blade.php`
  - `contracts/index.blade.php`
  - `contracts/show.blade.php`
  - `devices/index.blade.php`
  - `devices/show.blade.php`
  - `employees/index.blade.php`
  - `activities/index.blade.php`
  - `profile/index.blade.php`
  - `mcus/index.blade.php`

## Component Hierarchy

- Layout layer:
  - `layouts/app.blade.php`
  - `partials/sidebar.blade.php`
  - `partials/navbar.blade.php`
  - `partials/footer.blade.php`
- Reusable UI components:
  - `components/panel.blade.php`
  - `components/kpi-card.blade.php`
  - `components/status-badge.blade.php`
- Page-level composition:
  - `x-panel` wraps most card and table sections.
  - `x-kpi-card` renders KPI tiles on dashboard and inventory/device overview pages.
  - `x-status-badge` renders status labels across most tables and detail pages.
- Shared layout data:
  - `AppServiceProvider` injects `currentUser` and `navNotifications` into `layouts.*`.

## Routing

- Routing is Blade-driven through named routes in `route(...)` helpers.
- Main navigation routes:
  - `dashboard`
  - `products.index`
  - `inventory.index`
  - `batches.index`
  - `customers.index`
  - `contracts.index`
  - `devices.index`
  - `mcus.index`
  - `employees.index`
  - `activities.index`
  - `profile.index`
  - `logout`
- Form targets used in views:
  - `auth.login`
  - `products.store`, `products.destroy`
  - `categories.store`, `categories.destroy`
  - `inventory.adjust`
  - `batches.store`, `batches.destroy`
  - `customers.store`, `customers.destroy`
  - `contracts.store`, `contracts.destroy`
  - `devices.store`, `devices.destroy`, `devices.replace`
  - `employees.store`, `employees.destroy`
  - `profile.update`, `profile.updatePassword`
  - `mcus.store`, `mcus.destroy`
- Detail-page links:
  - `customers.show`
  - `contracts.show`
  - `devices.show`
  - `batches.show`

## API Calls

- No frontend AJAX API calls are present in `resources/js/app.js`.
- No `fetch`, `axios`, or `$.ajax` usage appears in the source views or source JS.
- The UI is form-post driven and route-driven rather than SPA/API driven.
- The only script-driven interactions are modal population, table filtering, and chart rendering.

## Forms

### Authentication

- `auth/login.blade.php`
  - POSTs to `auth.login`.
  - Fields: `email`, `password`, `remember`.

### Products

- `products/index.blade.php`
  - Create form POSTs to `products.store`.
  - Edit form is assigned `action="/products/{id}"` by modal script and submits PUT via method spoofing.
  - Fields: `code`, `name`, `category_id`, `model`, `capacity`, `unit`, `price`, `status`.

### Categories

- `categories/index.blade.php`
  - Create form POSTs to `categories.store`.
  - Edit form is assigned `action="/categories/{id}"`.
  - Fields: `name`, `description`, `status`.

### Inventory

- `inventory/index.blade.php`
  - Adjust form is assigned `action="/inventory/{id}"`.
  - Fields shown: `quantity_change`, `note`.
  - The form is a modal workflow tied to a selected inventory row.

### Batches

- `batch/index.blade.php`
  - Create form POSTs to `batches.store`.
  - Edit form is assigned `action="/batches/{id}"`.
  - Fields: `batch_code`, `supplier_id`, `import_date`, `expiry_date`, `quantity`, `note`.

### Customers

- `customers/index.blade.php`
  - Create form POSTs to `customers.store`.
  - Edit form is assigned `action="/customers/{id}"`.
  - Fields: `customer_code`, `customer_name`, `phone`, `email`, `address`, `type`, `status`.

### Contracts

- `contracts/index.blade.php`
  - Create form POSTs to `contracts.store`.
  - Edit form is assigned `action="/contracts/{id}"`.
  - Fields: `contract_code`, `customer_name`, `device_ids[]`, `mcu_ids[]`, `contract_type`, `maintenance_cycle_months`, `start_date`, `install_date`, `end_date`, `amount`, `status`.
  - The create form supports adding/removing device-MCU pairs client-side.

### Devices

- `devices/index.blade.php`
  - Create form POSTs to `devices.store`.
  - Edit form is assigned `action="/devices/{id}"`.
  - Fields: `device_code`, `serial_number`, `product_id`, `mcu_id`, `customer_id`, `contract_id`, `batch_id`, `import_date`, `install_date`, `firmware_version`, `location`, `status`.
- `devices/show.blade.php`
  - Replace form POSTs to `devices.replace`.
  - Fields: `product_id`, `mcu_id`, `install_date`.

### Employees

- `employees/index.blade.php`
  - Create form POSTs to `employees.store`.
  - Edit form is assigned `action="/employees/{id}"`.
  - Fields: `employee_code`, `full_name`, `position`, `phone`, `email`, `address`, `hire_date`, `role_id`, `status`.

### Profile

- `profile/index.blade.php`
  - Avatar upload form POSTs to `profile.update` and submits immediately on file selection in the avatar card.
  - Profile update form POSTs to `profile.update`.
  - Password update form POSTs to `profile.updatePassword`.
  - Fields: `avatar`, `username`, `email`, `current_password`, `password`, `password_confirmation`.

### MCU

- `mcus/index.blade.php`
  - Create form POSTs to `mcus.store`.
  - Edit form is assigned `action="/mcus/{id}"`.
  - Fields: `mcu_code`, `serial_number`, `firmware_version`, `status`.

## UI Workflow

- Login flow:
  - User opens `/login`.
  - Submits credentials.
  - Success redirects into the authenticated shell.
- Shell navigation:
  - Sidebar drives route-based page switching.
  - Navbar exposes profile and logout.
  - Breadcrumbs show page position.
- Dashboard flow:
  - Loads KPI cards, charts, recent activity, recent maintenance, and expiring contracts.
  - Charts are rendered client-side from Blade JSON payloads.
- Table workflow:
  - Most list pages use `data-datatable`.
  - Search inputs use `data-dt-search`.
  - Select filters use `data-dt-filter`.
- Modal edit workflow:
  - Row action buttons open Bootstrap modals.
  - Inline scripts copy row data into modal inputs.
  - The form `action` is set dynamically to the selected record URL.
- Delete workflow:
  - Delete actions use POST forms with `@method('DELETE')`.
  - Most deletes include a confirm dialog.
- Inventory adjustment:
  - A row button opens a modal.
  - Existing values are injected into readonly fields.
  - Adjustments are submitted as PATCH.
- Device replacement:
  - Device detail page exposes a replace modal when the device is active.
  - The modal includes MCU search/filter controls.
  - The form submits to the replacement endpoint.
- Profile maintenance:
  - Avatar can be uploaded separately or with the main profile form.
  - Password update is handled in a dedicated form.

## JavaScript

### `public/js/app.js`

- Responsibility:
  - Sidebar toggle behavior.
  - DataTables initialization.
  - ApexCharts helper wrappers.
- Public behavior:
  - Uses `[data-toggle-sidebar]` buttons to collapse/open the sidebar.
  - Persists desktop sidebar state in `localStorage`.
  - Enables `data-datatable` tables with external search/filter controls.
  - Exposes `window.SW.areaChart`, `SW.lineChart`, `SW.barChart`, and `SW.donutChart`.
- Dependencies:
  - jQuery
  - DataTables
  - ApexCharts
- Response flow:
  - No network or API calls.
  - Produces only UI interactions and chart rendering.

### `resources/js/app.js`

- Empty source entry in this checkout.
- No frontend logic is defined there.

## CSS

### `public/css/app.css`

- Responsibility:
  - Core theme and layout styling.
  - Sidebar, navbar, card, table, badge, timeline, auth page, and responsive behavior.
- Key structure:
  - CSS variables define the design system.
  - `.app-shell`, `.app-sidebar`, `.app-main`, `.app-navbar`, `.app-content`, and `.app-footer` define the shell.
  - `.auth-wrap`, `.auth-side`, and `.auth-card` define the login page.
  - `.card`, `.table`, `.badge-status`, `.timeline`, `.kpi-card` define reusable visual patterns.
- Responsive behavior:
  - Collapsible sidebar on desktop.
  - Off-canvas sidebar on mobile.
  - Single-column login layout on small screens.

### `resources/css/app.css`

- Tailwind import entry only.
- No page-specific rules are defined there in this checkout.

## Frontend Structure Summary

- The frontend is server-rendered Blade with shared layout composition.
- The UI is modular, card-based, and table-heavy.
- Navigation and CRUD flows are form-driven, not SPA-driven.
- JavaScript is limited to reusable shell interactions and chart/table helpers.
- No browser-side API integration is implemented in the source frontend files.

