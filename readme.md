# stormcaster
[![Build Status](https://travis-ci.org/greenelfx/stormcaster.svg)](https://travis-ci.org/greenelfx/stormcaster)
[![StyleCI](https://styleci.io/repos/48382253/shield?branch=master)](https://styleci.io/repos/48382253)

stormcaster is an API for MapleStory servers. This API can be used to rapidly build web apps using popular front end technologies such as Angular and React.

Please read the [documentation](https://github.com/greenelfx/stormcaster/wiki/Documentation) to learn more about stormcaster.

## Installation
To install stormcaster, you must first install [Composer](http://getcomposer.org). Then, clone this repository and run `composer update`. Next, run `php artisan key:generate`, `php artisan jwtkey:generate`, and `php artisan migrate`. Finally, rename `.env.example` to `.env`, and change the database connection information.

Start your web server, and you should now be able to make requests to stormcaster via Postman or `curl`.
To check that stormcaster is properly running, you can navigate to localhost/stormcaster and check for the welcome message.

## Security Vulnerabilities

If you discover a security vulnerability within stormcaster, please create a Github issue immediately.

### License

stormcaster is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)