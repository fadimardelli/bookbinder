# BookBinder

**A social web application for book lovers.** Users discover books available in local libraries,
rate and review them, keep a list of favourites, and organise or join book-club meetings at a library.

Built with **Symfony 6.2 (PHP 8.1)**, **Doctrine ORM**, **Twig** and **Bootstrap 5** as the team project
for the *Software Engineering & Web Technology* course at KU Leuven (Faculty of Engineering Technology,
Campus Group T), academic year 2022–2023.

![BookBinder home page](docs/screenshots/home.jpg)

---

## Contents

- [Features](#features)
- [Screenshots](#screenshots)
- [Architecture](#architecture)
- [Data model](#data-model)
- [Project structure](#project-structure)
- [Running the project](#running-the-project)
- [Tests](#tests)
- [Team and contributions](#team-and-contributions)

---

## Features

| Area | Functionality |
|---|---|
| **Accounts** | Registration, login and logout with a custom form authenticator, CSRF protection and hashed passwords. Every page except login and registration requires an authenticated user. |
| **Home page** | Profile summary, the user's upcoming meetings and favourite books, a *Trending* carousel, the newest books, a carousel of all upcoming meetings and a YouTube widget with book reviews. |
| **Book catalogue** | All books, 30 per page with previous and next navigation. Filter by title, author and library. A search bar in the navigation is available on every page. |
| **Book page** | Cover, author and average rating, the libraries that hold the book with the number of copies remaining, reviews by other users, a review form with a star rating, and add or remove from favourites. |
| **Meetings** | Create a meeting for a book at a library on a chosen date (pop-up form on the home page). Search meetings by host, library or book title, and join them. A meeting disappears from the list once joined. |
| **Profile** | Meetings the user attends and meetings the user hosts. Leave or cancel a meeting with one click. Edit the bio and see favourite books. |

## Screenshots

| | |
|---|---|
| **Book catalogue with filters**<br>![Books](docs/screenshots/books.jpg) | **Book page**<br>![Book details](docs/screenshots/book-details.jpg) |
| **Upcoming meetings**<br>![Meetings](docs/screenshots/meetings.jpg) | **Profile**<br>![Profile](docs/screenshots/profile.jpg) |
| **Creating a meeting**<br>![Create meeting](docs/screenshots/create-meeting.jpg) | **Login**<br>![Login](docs/screenshots/login.jpg) |

## Architecture

BookBinder follows Symfony's **model–view–controller** structure. Controllers handle routing and request
logic, Doctrine entities and repositories form the model, and Twig templates render the pages. A few
interactive actions (favourites, creating and joining meetings) are sent from JavaScript to JSON endpoints
with `fetch`, so the page does not reload.

```mermaid
flowchart LR
    Browser["Browser<br/>Twig pages, Bootstrap 5,<br/>vanilla JS"] -- "HTTP / form posts" --> FW
    Browser -- "fetch (JSON)" --> FW
    subgraph Symfony["Symfony 6.2 application"]
        FW["Security firewall<br/>LoginFormAuthenticator<br/>access control"] --> C["Controllers<br/>src/Controller"]
        C --> T["Twig templates<br/>templates/"]
        C --> R["Repositories<br/>src/Repository"]
        R --> E["Doctrine entities<br/>src/Entity"]
    end
    E --> DB[("Database<br/>MySQL in production<br/>SQLite for the demo")]
```

### Routes

| Route | Controller | Purpose |
|---|---|---|
| `/login`, `/logout` | `SecurityController` | Authentication |
| `/register` | `RegistrationController` | New account (Symfony form with validation) |
| `/` | `HomeController` | Home page |
| `/books/{page}` | `BooksController` | Paginated catalogue and filters (title, author, library) |
| `/book/{id}` | `BookController` | Book page and review form |
| `/add-to-favorites`, `/remove-from-favorites` | `BookController` | JSON endpoints for favourites |
| `/searchresults` | `SearchResultsController` | Search from the navigation bar |
| `/meeting`, `/filtermeeting` | `MeetingController` | Meeting list, search and joining a meeting |
| `/addmeetingapi` | `AddMeetingApiController` | JSON endpoint that creates a meeting |
| `/profile` | `ProfileController` | Profile page, bio editing, leaving or cancelling meetings |

### Front end

- **Templates:** `templates/base.html.twig` defines the layout and navigation bar. The home page is assembled from reusable components in `templates/Components/`: the trending, newest-books, meetings and profile sections, and the meeting and profile pop-ups.
- **Styling:** Bootstrap 5 provides the grid and components, together with custom styles in `public/css/`.
- **Scripts:** small JavaScript modules in `public/js/` handle the star rating, joining meetings, the profile pop-up and the trending carousel.
- **Libraries:** [Choices.js](https://github.com/Choices-js/Choices) provides the searchable book and library selects in the meeting form, and Font Awesome provides the icons.

## Data model

Eight Doctrine entities. Many-to-many relations (users ↔ meetings, users ↔ favourite books,
books ↔ libraries) are modelled as explicit join entities so they can carry extra data, such as the
number of copies a library has.

```mermaid
erDiagram
    USER ||--o{ REVIEW : writes
    USER ||--o{ MEETING : hosts
    USER ||--o{ ATTENDEE : "takes part as"
    USER ||--o{ FAVORITE_BOOK : marks
    BOOK ||--o{ REVIEW : receives
    BOOK ||--o{ FAVORITE_BOOK : "is marked in"
    BOOK ||--o{ AVAILABILITY : "is stocked as"
    BOOK ||--o{ MEETING : "is discussed in"
    LIBRARY ||--o{ AVAILABILITY : holds
    LIBRARY ||--o{ MEETING : hosts
    MEETING ||--o{ ATTENDEE : has

    USER {
        int id
        string email
        string username
        string password
        json roles
        text bio
    }
    BOOK {
        int id
        text title
        string author
        float average_rating
        bool trending
        text cover_image
    }
    LIBRARY {
        int id
        string name
        string location
    }
    AVAILABILITY {
        int total_count
        int current_count
    }
    REVIEW {
        text review
        float rating
    }
    MEETING {
        datetime date
        text description
    }
```

The database schema is generated from these entities. The `migrations/` folder holds the original MySQL
migrations used on the course server.

## Project structure

```text
.
├── src/
│   ├── Controller/      request handling for each page and the JSON endpoints
│   ├── Entity/          Doctrine entities (data model)
│   ├── Repository/      database queries (Doctrine QueryBuilder)
│   ├── Form/            registration form type
│   └── Security/        login form authenticator
├── templates/           Twig views, including reusable components in templates/Components/
├── public/              front controller (index.php), CSS, JavaScript, images, Bootstrap
├── config/              Symfony configuration (security, Doctrine, routing, Twig)
├── migrations/          Doctrine migrations (MySQL)
├── tests/               PHPUnit tests: controller access tests and entity tests
├── demo/                demo data (books, libraries, users, reviews, meetings)
├── scripts/             one-step scripts to run the demo locally
└── docs/screenshots/    images used in this README
```

## Running the project

The application was originally deployed on the university's student server with a MySQL database.
Neither is available any more, so the repository can run locally with an **SQLite** database and
**demo data**. No database server is needed.

### Requirements

- PHP **8.1 or newer** with the `pdo_sqlite` extension
- [Composer](https://getcomposer.org/)

### Quick start

```bash
git clone https://github.com/fadimardelli/bookbinder.git
cd bookbinder
composer install
```

Then start the demo:

| Windows | macOS / Linux |
|---|---|
| Double-click `scripts\run-demo.bat` | `./scripts/run-demo.sh` |

The script creates `var/demo.db`, loads the demo data, starts PHP's built-in web server and opens
**http://localhost:8000**.

**Demo account:** `test@test.com` / `test1234`. You can also register a new account.

To reload a fresh copy of the demo data, run `scripts\reset-demo.bat` or `./scripts/run-demo.sh reset`.
Meeting dates are set relative to the day the data is loaded, so meetings always appear upcoming.

### Manual setup

```bash
composer install
php bin/console doctrine:schema:create
php -r '$db = new PDO("sqlite:var/demo.db"); $db->exec(file_get_contents("demo/demo-data.sql"));'
php -S localhost:8000 -t public
```

### Using MySQL instead of SQLite

Override `DATABASE_URL` in a `.env.local` file, for example
`mysql://user:password@127.0.0.1:3306/bookbinder?serverVersion=8.0.32&charset=utf8mb4`, then run
`php bin/console doctrine:database:create` and `php bin/console doctrine:migrations:migrate`.
The demo data file is written for SQLite. Books, libraries and users can be added through the site or
with your own data.

### About the demo data

The demo contains 40 well-known books, 5 libraries in Leuven, Mechelen and Ghent, 5 fictional users,
reviews and meetings. Book covers are loaded from the [Open Library Covers API](https://openlibrary.org/dev/docs/api/covers)
by ISBN, and profile avatars are generated by [RoboHash](https://robohash.org/). All demo accounts use
the password `test1234`.

## Tests

The `tests/` folder holds two PHPUnit suites:

- **`tests/Controller`:** functional tests with Symfony's `WebTestCase`. They log in as the demo user and check that every page responds successfully.
- **`tests/EntityTests`:** unit tests for the entities' getters and setters.

```bash
composer install
php bin/phpunit
```

The tests use the same SQLite demo database, configured in `.env.test`, so start the demo once first to
create it.

## Team and contributions

BookBinder was built by a team of **four students** over an eight-week project (April–June 2023). The
team used feature branches and merge requests on the course GitLab, with around 140 commits. This
repository contains the final version of the code. It was packaged to run locally with SQLite and demo
data after the original hosting was taken offline.

**My contributions ([Fadi Mardelli](https://github.com/fadimardelli)):**

- **Setup:** set up the Symfony project and its front-end foundation.
- **Templates and UI:** built the Bootstrap layout, the navigation bar component, and the login and sign-up screens.
- **Pages:** built the home page, the book catalogue, the meetings page and the profile page.
- **Home page data:** loaded the trending and newest books from the database.
- **Book page:** built the individual book page, and linked the favourite books on the profile page.
- **Catalogue features:** wrote the catalogue's pagination and the book search tool.
- **Meetings:** built the meeting pop-up and the create-meeting function.
- **Profile:** added profile editing.
- **Release:** did the final integration and deployment fixes.
