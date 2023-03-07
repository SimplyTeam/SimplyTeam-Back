# Fix
## testing environment
1. Create file `.env.testing` using testing environment;
2. Set config cache to testing
```shell
$ php artisan config:cache --env=testing
```
3. Run testing
```shell
$ php artisan test
```
