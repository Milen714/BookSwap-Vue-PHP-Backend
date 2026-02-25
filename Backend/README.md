# BookSwap - Book Exchange Platform

BookSwap is a community-driven book exchange platform where users can list their books, browse available titles, and request swaps with other readers. The platform facilitates the entire swap process from request to shipping.

# TEST ACCOUNTS:

**AdminTestAccount:** test@admin.mail **Password:** 1234
**UserTestAccount:** test@user.mail **Password:** 1234

## Table of Contents

- [Features](#features)
- [Setup](#setup)
- [Architecture](#architecture)
- [Technical Implementation](#technical-implementation)
- [Security Implementation](#security-implementation)
- [WCAG Accessibility Compliance](#wcag-accessibility-compliance)
- [GDPR Compliance](#gdpr-compliance)

---

## Features

- **Book Listing**: Add books by ISBN with automatic metadata fetching from Google Books API
- **Browse & Search**: View, search, and sort all available books shared by the community
- **Swap Requests**: Request books from other users for only the cost of shipping (Next steps are to implement a feature where it's possible to arrange personal pickup or meeting at a determined location for the exchange to happen) This could be a two-way messaging system
- **Payment Integration**: Stripe checkout for shipping cost payments
- **Email Notifications**: Automated emails for swap requests and password resets
- **Token Economy**: Users earn and spend swap tokens for exchanges
- **User and Role Authentication**: Secure login, registration, and password reset (Next steps: Admin Dashboard, accessible only to well... Admins. Will include some metrics like book post and request per user and other statistics and metrics)
- **Barcode Scanner**: To make listing your books easier on mobile devices, the book could be scanned with the device camera. (Works only through HTTPS (no clue why). Testing it in a local environment is difficult.) If desired the feature can be tested on https://bookswap.art/ (No guarantee for the uptime of this link since it's hosted on my personal PC)
- **Light/Dark Themes**: The use of Tailwind made the addition of a Dark theme a breeze. The themes can be changed from the top navigation before and after login. Once a theme has been selected, a persistent cookie is saved for 30 days and survives browser exit.

---

## Setup

### Installation

1. Clone the repository

2. Create your local environment file:

```bash
cp .env.example .env
```

Update `.env` with your local credentials (DB, Stripe, mail).

3. Start Docker containers:

```bash
docker compose up
```

> **Note:** Composer dependencies are **automatically installed** on first container start.

4. Import the database schema from `DataBaseDump.sql` in the root of the app via PHPMyAdmin at [localhost:8080](http://localhost:8080), using Username: root, Password: secret123. **The Dump should be imported into the developmentdb**

5. TEST ACCOUNTS:
   **AdminTestAccount:** test@admin.mail **Password:** 1234
   **UserTestAccount:** test@user.mail **Password:** 1234
   > **Note:** Do not get surprised if, for some reason, you test the password recovery option or the notification system, and you receive an email from GardenGroup@gmail.com, since that was an email created for the NoSql Project. I just decided to use it here to demonstrate the functionality.

---

## Architecture

The standard MVC architecture built during the classes of term 2 was used for this app.
There were some "quality of life" changes that I have made to it:

- **Role-Based Authentication**: Enabled by the PHP 8's attributes, a middleware between each request was implemented, checking if the user is logged in and if their role is suitable to access the requested endpoint.

```php
class RoleMiddleware{
    public function __construct(public AuthService $authService){}
    public function check(object $controller, string $methodName){
        try {
        $reflectionMethod = new ReflectionMethod($controller, $methodName);

The function check(object $controller, string $methodName) is called in the index.php. Inside the reflection method reads the values passed in the attributes decorating the methods in the controllers. In this example The values are the UserRoles Enum. It is optional, but multiple roles can be passed in for multi-role access.

#[Attribute(Attribute::TARGET_METHOD)]
class RequireRole {
    public array $roles;
    public function __construct(UserRole|array $roles) {
        $this->roles = is_array($roles) ? $roles : [$roles];
    }
}
```

Example use in a controller:

```php
#[RequireRole([UserRole::ADMIN])]
    public function dashboard() {
        echo "Welcome Admin!";
    }
    OR
    #[RequireRole([UserRole::USER, UserRole::ADMIN])]
    public function viewMySwapRequests($vars = []){
        $userId = $vars['id'] ?? null;

    Allowing users who are admins or normal users to access this method
```

- **Base Controller**: Base controller implemented to make the use of shared layout easier. This is something like return View() in asp.NET or this was what I was going for.

```php
namespace App\Controllers;

class Controller
{
    protected function view($viewName, $vars = [], $layout = 'layouts/mainLayout')
    {
        // Load the view and capture output
        ob_start();
        extract($vars);

        require __DIR__ . '/../../Views/' . $viewName . '.php';
        // ob_get_clean(); prevent double output and turns the inside of the require ^^ into a string which is stored in $content
        $content = ob_get_clean();

        // Load the layout with the content
        extract(array_merge($vars, ['content' => $content]));
        require __DIR__ . '/../../Views/' . $layout . '.php';
        // The layout will use the $content variable to display the view content in the main tag
        //Layout is footer and header around the content
    }
}
```

Use in a controller method
This allows you to set the title and pass vars to the view while including the header and footer layouts in a single line.

```php
public function addBook($vars = [])
    {
        $error = isset($vars['error']) ? urldecode($vars['error']) : null;

        $this->view('Book/AddBook', [ 'title' => 'Add Book Page', 'error' => $error] );
    }
```

---

## Technical Implementation

### Stripe Checkout for collecting shipping costs.

Users can swap books from the comfort of their homes. To deliver the books, a shipping service such as PostNL can be used. The shipping fee is paid by the user requesting the book (Just like buying an item on Maarkplaats). For that reason, and because of Stripe's simple, well-documented checkout service, it is implemented in the project.
**Stripe:** Stripe collects the payment for the shipping calculated by the MockPostNLService.
**Note:** In a real implementation, the shipping fee is forwarded to the BookSwaps bank account attached to Stripe. After which, the PostNL API is called to notify them of the purchase of a shipping label. PostNL takes note of the purchase, but BookSwap is not yet charged for the label. Rather, PostNL notes the transaction for the specific label in a tab. Depending on the contract between PostNL and the webshop BookSwap, in this case, the charges are made in a single bulk transaction for all generated shipping labels.
**MockPostNLService:** `app\src\Services\MockPostNlService.php` This service is for demonstration purposes only and only calculates the shipping cost based on the book's weight. The weight is calculated based on the number of pages. If Google's BookAPI does not return the page count, it is estimated based on research into how many pages the average book has (Spoiler: it was a range), then a random number from that range is selected. This weight estimate is not accurate in any way and is only for demonstration purposes, since the real PostNLAPI does not have a developer(test) mode without a contract with them (as far as I know).

### API Endpoints (JSON)

| Endpoint                  | Method | Description                                   | File Reference              |
| ------------------------- | ------ | --------------------------------------------- | --------------------------- |
| `/fetchBookPreview`       | POST   | Fetch book data by ISBN from Google Books API | `BookController.php`        |
| `/scanBook/{isbn}`        | GET    | Scan barcode and retrieve book info           | `BookController.php`        |
| `/getProfileAddress/{id}` | GET    | Get user's saved address                      | `UserController.php`        |
| `/getUserTokens`          | GET    | Get user's swap token balance                 | `UserController.php`        |
| `/createBookRequest`      | POST   | Create a new book swap request                | `BookRequestController.php` |
| `/updateRequest`          | POST   | Update swap request status                    | `BookRequestController.php` |

### JavaScript Dynamic Updates

**Fetch API for AJAX Requests** (`app/public/Js/`):

- `BookDetailsModal.js` - Fetches profile address without page reload
- `updateRequestStatus.js` - Updates swap status via API
- `createBookPreview.js` - Dynamic DOM manipulation for book preview
- `checkout.js` - Stripe embedded checkout integration
- `userTokens.js` - Real-time token balance updates

Example (`updateRequestStatus.js`):

```javascript
fetch(`/updateRequest?requestId=${requestId}&status=${nextStatus}`, {
  method: "POST",
  headers: { "Content-Type": "application/json" },
  body: JSON.stringify({ status: nextStatus, requestId: requestId }),
})
  .then((response) => response.json())
  .then((data) => location.reload());
```

### CSS Framework & Transitions

- **Tailwind CSS** Tailwind classes: (`app/public/css/style.css`)
- Custom transitions in `app/public/css/custom.css`:

```css
.flip-card {
  transition: transform 1s cubic-bezier(0.3, 0.8, 0.2, 1.3);
  transform-style: preserve-3d;
}
.flip {
  transform: rotateY(180deg);
}
```

---

## Security Implementation

### SQL Injection Prevention

All database queries use **parameterized statements** (`app/src/Repositories/UserRepository.php`):

```php
$query = 'SELECT * FROM users WHERE email = :email';
$stmt = $pdo->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();
```

### XSS Prevention

All output includes `htmlspecialchars()` (`app/Views/Book/BookDetailsModal.php`):

### Password Security

Passwords hashed with bcrypt (`app/src/Models/User.php`):

```php
$user->password_hash = password_hash($_POST['password'] ?? '', PASSWORD_BCRYPT);
```

Verification (`app/src/Services/UserService.php`):

```php
if ($user && password_verify($password, $user->password_hash)) {
    return $user;
}
```

### Session Security

Secure session configuration (`app/config/config.php`):

---

## WCAG Accessibility Compliance

An effort towards accessibility has been made through the use of ARIA attributes and screen-reader-only spans.

### Semantic HTML & ARIA Attributes

**Navigation** (`app/Views/layouts/mainLayout.php`):

```html
<button aria-controls="navbar-multi-level-dropdown" aria-expanded="false">
  <span class="sr-only">Open main menu</span>
</button>
<a href="/" aria-current="page">Browse</a>
```

**Interactive Elements**:

- `aria-expanded` for dropdown menus
- `aria-hidden="true"` for decorative icons
- `aria-labelledby` for associating labels

### Alternative Text for Images

```html
<img src="/Assets/BookSwapLogo.svg" class="h-7" alt="BookSwap Logo" />
```

### Screen Reader Support

Hidden text for screen readers using Tailwind's `sr-only` class:

```html
<span class="sr-only">Toggle user menu</span>
```

### Color Contrast

Dark theme with sufficient contrast ratios:

- Text: `text-white` on `bg-[#0F0F0F].`
- Muted text: `text-[#7b8186]` for secondary information
- Interactive elements: `text-blue-600` for active states

### Responsive Design

Mobile-first responsive breakpoints: Tailwind made it really easy to implement responsive elements and components, light and dark themes

The simple line bellow includes a media query, making the div flex-row on desktop and flex-col(column) on mobile.

```html
<div class="hidden md:block">Desktop navigation</div>
<div class="flex flex-col md:flex-row">Responsive layout</div>
```

---

## GDPR Compliance

### Data Minimization

Only essential user data is collected (`app/src/Models/User.php`):

- Name, email, address (required for shipping)
- Password stored only as bcrypt hash
- No unnecessary tracking or analytics

### Secure Data Storage

- Passwords never stored in plain text
- Session data secured with httponly cookies
- Database credentials isolated in config files

### User Rights Implementation

**Password Reset**: Secure token-based reset (`app/src/Controllers/AccountController.php`):

```php
$token = $this->generateSecureToken();
$user->resset_token = $token;
$user->resset_token_expiry = new \DateTime('+1 hour');
```

### Secure Communication

- Password reset links sent via secure email service
- Tokens expire after 1 hour
- One-time use tokens cleared after password change:

```php
$user->resset_token = null;
$user->resset_token_expiry = null;
```

### Data Protection in Transit

- Session cookies marked as httponly
- Input validation on all user-submitted data

---
