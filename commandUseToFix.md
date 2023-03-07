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

## Docker-compose won't launch with plage /24
It must have ip subnet /16 with
```yaml
xxxxxx:
    networks:
        sail:
            ipv4_address: 172.21.XX.XX


networks:
    sail:
        driver: bridge
        ipam:
            config:
                - subnet: 172.21.0.0/16
```

## There is no existing directory at \"/home/tim/Ynov/M2/FinalProject/SimplyTeam-Back/storage/logs\" and it could not be created: Permission denied"
```shell
php artisan route:clear

php artisan config:clear

php artisan cache:clear
```
