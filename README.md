# JET
PHP Jet Engine 2.0

## Setup

1. Update .htaccess (if using apache) to replace "domain" with your domain

1. Run `composer update` followed by `composer install`

1. Add jwt ES384 pub/priv keys
```
ecparam -genkey -name secp384r1 -noout -out ec384-private.pem 
openssl ec -in ec384-private.pem -pubout -out ec384-public.pem
```

1. Add .env file or run install to auto generate

1. Run `npm install` to get gulp 
