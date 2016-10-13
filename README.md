# skeleton-app
A default set-up to quickly start a new PHP application

## Usage
Create a new project with Composer:

```sh
composer create-project -s dev basje/skeleton-app
```

The `-s dev` part is temporary while I am working towards a somewhat usable first version. 

When asked `Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]?`, please type `Y`. This will remove all references to this repository and you will be able to start your own clean project. You can optionally create a new git repo with `git init`, or use your own prefered version control system.

## Included functionality

### Routing

- [Route](http://route.thephpleague.com/) by the [PHP League](http://thephpleague.com/): `league/route` [[source](https://github.com/thephpleague/route)]
    - Build on top of [FastRoute](https://nikic.github.io/2014/02/18/Fast-request-routing-using-regular-expressions.html) by [Nikita Popov](https://nikic.github.io/): `nikic/fast-route` [[source](https://github.com/nikic/FastRoute)]
- PSR-7 [definition of HTTP messages](http://www.php-fig.org/psr/psr-7/): `psr/http-message` [[source](https://github.com/php-fig/http-message)]
- PSR-7 [implementation of HTTP messages](https://docs.zendframework.com/zend-diactoros/): `zendframework/zend-diactoros` [[source](https://github.com/zendframework/zend-diactoros)]

### Dependency Injector

- Auryn: `rdlowrey/auryn`

### Several Services

- Logging: `monolog/monolog`
- JSON Web Tokens: `firebase/php-jwt`
- UUID generator: `ramsey/uuid`

## Further Reading

- [Building better project skeletons with Composer](https://www.binpress.com/tutorial/better-project-skeletons-with-composer/157)
- [Semantic Versioning](http://semver.org/)
- [Keep a CHANGELOG](http://keepachangelog.com/)
