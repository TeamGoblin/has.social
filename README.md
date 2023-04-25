# JET
PHP Jet Engine

## Setup

1. Update .htaccess (if using apache) to replace "domain" with your domain

1. Run `composer update` followed by `composer install`

1. Create `.db.env` and `.base.env` from examples - Set the home dir in base env to identify where to save tmp session files

1. Its set to use sass with the "compass watch" command in the root dir.  Basically you're on a mac, you should have compass installed (its a ruby gem or some shit) - when you change the SASS files in cargo just go to the root dir where config.rb is and run "composer watch" and it'll auto generate your CSS files and put them in the right spot