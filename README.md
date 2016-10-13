# skeleton-app
A default set-up to quickly start a new PHP application

## Usage
Create a new project with Composer:

```
composer create-project -s dev basje/skeleton-app
```

The `-s dev` part is temporary while I am working towards a somewhat usable first version. 

When asked `Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]?`, please type `Y`. This will remove all references to this repository and you will be able to start your own clean project. You can optionally create a new git repo with `git init`, or use your own prefered version control system.

## Included functionality

### Routing

- Route by the PHP League: `league/route`
    - Build on top of FastRoute by Nikita Popov: `nikic/fast-route`
- PSR-7 definition of HTTP messages: `psr/http-message`
- PSR-7 implementation of HTTP messages: `zendframework/zend-diactoros`

### Dependency Injector

- Auryn: `rdlowrey/auryn`

### Several Services

- Logging: `monolog/monolog`
- JSON Web Tokens: `firebase/php-jwt`
- UUID generator: `ramsey/uuid`
