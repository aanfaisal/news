## Basic Features:

* Manage posts and media
* Categorize posts
* User Roles
* Content moderation
* Markdown Editor
* Amazon S3 integration

## Installation

Download this repo.

Rename `.env.example` to `.env` and fill the options.

Run the following commands:

```
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
gulp
php artisan serve
```

If you are making changes to JavaScript or Styles make sure you run `gulp watch`.

## Issues

For technical questions and bugs feel free to open one issue.

