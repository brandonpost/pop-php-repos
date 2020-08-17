# Popular PHP Repositories on GitHub

## Architecture

### Back-End

This project utilizes the [Symfony Framework](https://symfony.com) (LTS v4.4)
to take advantage of its many components, such as:
- the [Routing component](https://symfony.com/doc/4.4/create_framework/routing.html) to map paths to controller actions.
- the [Form component](https://symfony.com/doc/4.4/components/form.html) to generate html forms and process user input.
- the [HTTP Client component](https://symfony.com/doc/4.4/http_client.html) to communicate with external APIs.

[Doctrine ORM](https://www.doctrine-project.org/) is used for interaction with
the MySQL database and for mapping relationships between database fields and
entity properties.

The primary back-end files of interest for this project are:
- src/Controller/GithubRepoController.php
- src/Entity/GithubRepo.php

### Front-End

This project uses the following front-end technologies:
- [Twig](https://twig.symfony.com/) template engine
- [Bootstrap 4](https://getbootstrap.com) CSS and JS
- [jQuery 3.5.1](https://jquery.com)
- [DataTables](https://datatables.net) jQuery plugin

The primary front-end files of interest are:
- templates/base.html.twig
- templates/github_repo/index.html.twig
- templates/github_repo/detail.html.twig
- public/js/app.js

## Live Demo

A live demo of this project can be viewed at [https://victr.brandonpost.com](https://victr.brandonpost.com).

## Installation

1. Create an empty MySQL database (using phpMyAdmin or MySQL CLI).
2. Clone the GitHub repository to your local environment.
3. In a command line (terminal) window, navigate to the project folder.
    > cd /path/to/project/folder
4. Install the composer packages.
    > composer install
5. Create a local environment settings file by running this command:
    > composer dump-env prod
6. Edit the .env.local.php file just created by the previous command and enter the correct values in the DATABASE_URL line.
7. Allow Doctrine to create database tables by running this command (type Y to confirm if prompted):
    > php bin/console doctrine:migrations:migrate
8. Navigate to the public folder within the project:
    > cd public
9. Run the following command to start PHP's built-in web server:
    > php -S localhost:8000
10. Open a browser window and enter the following into the address bar:
    > http://localhost:8000