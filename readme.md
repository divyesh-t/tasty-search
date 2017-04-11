
### Getting started (developer)

You will need PHPStorm (or other IDE), php, mysql, git and beanstalkd. You will not need a local web-server (php has one built in).

1. Download and install [PHPStorm EAP](https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Early+Access+Program)
2. Create a SSH key-pair and add the pub-key to your account on GitHub (if you've not already done so)
    [GitHub SSH Settings](https://github.com/settings/ssh).

    On OSX, this is:
    `
    ssh-keygen
    cat ~/.ssh/id_rsa.pub | pbcopy
    `

    An SSH public key will have been created & copied to your clipboard.

3. Create a GitHub Personal access token <br>
   https://github.com/settings/tokens <br>
   The token should have access to private repos

#### OSX

1. Install homebrew (iff not already done)
    ```
    ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
    ```

1. Install php, git, mysql, redis & composer; start services; install dependencies
     ```
     brew install php71 git mysql composer composer-completion php71-intl redis
     brew services start mysql      #version 5.8 or better
     brew services start redis
     git clone git@github.com:divyesh-t/tasty-search
     composer config github-oauth.github.com <your personal access token>
     cp .env.example .env
     composer install               #install dependencies
     mysql -uroot < database/SQL/tables.sql
     php artisan migrate
     ```

2. Setup XDEBUG

    Edit ```/usr/local/etc/php/7.1/conf.d/ext-xdebug.ini```

     ```
     [xdebug]
     zend_extension="/usr/local/opt/php71-xdebug/xdebug.so"
     xdebug.remote_port=9001    #Port 9000 does not work for some reason
     xdebug.remote_enable=yes
     ```


#### Ubuntu

* Install php7 components.

```
    sudo LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php
    sudo apt-get update
    sudo apt-get install php7.1-fpm php7.1-mysql php7.1-curl php7.1-mcrypt php7.1-mbstring php7.1-xml php-xdebug php7.1-bcmath
    php -v #should reflect version 7
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
```

* Install whois

```
    sudo apt-get install whois
```
> edit or add /etc/whois.conf file by copying it from https://gist.github.com/thde/3890aa48e03a2b551374

* Install MySQL 5.7 & Redis

```
    wget https://dev.mysql.com/get/mysql-apt-config_0.6.0-1_all.deb
    dpkg -i mysql-apt-config_0.6.0-1_all.deb #select MySQL 5.7 if not already selected and apply.
    mysqladmin -u root -p version # MySQL Server Version should reflect 5.7.11
    sudo apt-get install build-essential tcl
    cd /tmp
    curl -O http://download.redis.io/redis-stable.tar.gz
    tar xzvf redis-stable.tar.gz
    cd redis-stable
    make
    make test
    sudo make install
    # follow instrucion from https://www.digitalocean.com/community/tutorials/how-to-install-and-configure-redis-on-ubuntu-16-04 to configure redis server
```

* Project Workspace setup

```
    git clone git@github.com:divyesh-t/tasty-search.git
    composer config github-oauth.github.com <your personal access token>
    cd tasty-search
    cp .env.example .env
    composer install    #install dependencies
    mysql -p -u root < database/SQL/tables.sql
    php artisan migrate
 ```

> edit .env to up enter DB_HOST and REDIS details to your local mysql and redis server respectively.
      create a database, user and password and save details in .env.


## Ingest data from dataset file

```
    php artisan ingest:docs [path/to/your/dataset] [count of docs to ingest?]
    #e.g. php artisan ingest:docs /path/to/dataset 100000
```

## start server

```
    php artisan serve --host=0.0.0.0
```

* Open http://localhost:8000 in browser


## Load Testing

1. Download and install

```
    wget http://download.joedog.org/siege/siege-latest.tar.gz
    tar -zxvf siege-latest.tar.gz
    cd siege-*/
    ./configure
    make
    sudo make install
```

2. Generate Test files

```
    php artisan generate:testFiles [count of test cases?] #if count not provided, default is 1000
    #e.g. php artisan generate:testFiles
```

3. Run tests
```
    siege -if {path/to}/tasty-search/public/tests.txt -c [cuncurrent user] -t[time to Rum(M|S|H)]
    #e.g. siege -if public/tests.text -c 10 -t10M
```
