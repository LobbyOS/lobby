# Testing

For testing, the repo directory shouldn't be used. Instead, we will move all files of Lobby to a temporary directory and run our tests in that directory.

When Lobby or apps are installed, extra files are added to the repo and when you push, these get into the commits. To prevent that we do as stated above.

For this, run `setup-tests.php` before running `phpunit`. When it is ran :

* Lobby repo is copied to a temporary location.
* A PHP server is started with this temporary location as document root (`127.0.0.1:8000`)

If code changes are made, then this lobby directory is un

## Requirements

Execute this in `testing` directory :

```bash
composer update
```

Then run `phpunit` :

```bash
vendor/bin/phpunit -c ./
```
