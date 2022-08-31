<h1 align="center">JOIN - Junior Online Integration Network</h1>

<p align="center">
  <img src="assets/images/logo_join/logo_join.png" alt="join-logo" height="120px"/>
  <br>
  <i>JOIN is recruitment management system designed specially for the junior enterprises
    </i>
  <br>
</p>

<p align="center">
  <a href="https://github.com/khaledlajili/join/issues">Submit an Issue</a>
</p>


<hr>

## Instalation

### Prerequisites

- Install PHP 8.x
- Install composer
- (Optional) Install Symfony console
- Install MySQL
- Install Yarn - Package Manager

### Setting Up the Project

**Download Composer dependencies**

Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```
You may alternatively need to run `php composer.phar install`, depending
on how you installed Composer.

**Database Setup**

Build the database and execute the migrations with:

```
# "symfony console" is equivalent to "bin/console"
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
```

Make sure to start your own database server and update the `DATABASE_URL` environment variable in
`.env` or `.env.local` before running the commands above.

**Start the Symfony web server**

You can use Nginx or Apache, but Symfony's local web server
works even better.

To install the Symfony local web server, follow
"Downloading the Symfony client" instructions found
here: https://symfony.com/download - you only need to do this
once on your system.

Then, to start the web server, open a terminal, move into the
project, and run:

```
symfony serve
```

(If this is your first time using this command, you may see an
error that you need to run `symfony server:ca:install` first).

Now check out the site at `https://localhost:8000`

**Webpack Encore Assets**

This app uses Webpack Encore for the CSS, JS and image files. But
to keep life simple, the final, built assets are already inside the
project. So... you don't need to do anything to get thing set up!

If you *do* want to build the Webpack Encore assets manually, you
totally can! Make sure you have [yarn](https://yarnpkg.com/lang/en/)
installed and then run:

```
yarn install
yarn build
```

**Create admin account**

Go to `https://localhost:8000/register/admin` and create the admin account.

:warning: Make sure you delete the `registerAdmin` function from the `RegistrationController` on production.


## Contributors

<p>
  <img src="assets/images/collaborators.png" alt="collaborators-logos" width="500px" height="auto">
</p>

- <a href="https://irisje.tn">IRIS Junior Entreprise</a>
- <a href="https://optimaje.com">OPTIMA Junior Entreprise</a>
- <a href="https://jetunisie.com">Junior Enterpises of Tunisia</a>
- <a href="https://codexje.tn">CODEX Junior Entreprise</a>
- <a href="https://enetcomje.com">ENET'COM Junior Entreprise</a>

## Have Ideas, Feedback or an Issue?

If you have suggestions or questions, please feel free to
open an issue on this repository or call us on (+216) 95 581 417
