<?php namespace {{ phpProjectNamespace }};

// Root namespaces of the main packages we use. Since this is where we tie all
// the packages together to get the app running, we use the full namespaces to
// define the objects, for optimal readability.
use Auryn;
use Composer;
use League;
use Psr;
use Zend;

class Application {

  /**
   * @var Composer\Autoload\ClassLoader
   */
  private $autoLoader;

  /**
   * Constructor.
   *
   * @param Composer\Autoload\ClassLoader $autoLoader
   */
  public function __construct( Composer\Autoload\ClassLoader $autoLoader ) {
    $this->setAutoLoader( $autoLoader );
  }

  public function run() {

    // Don't trust the server settings, choose your own default timezone.
    date_default_timezone_set( 'UTC' );

    // Create PSR-7 compliant request with all request data.
    $request = $this->buildRequest();
    // Create an empty PSR-7 compliant response.
    $response = new Zend\Diactoros\Response();
    // Create emitter to send responses back to client.
    $emitter = new Zend\Diactoros\Response\SapiEmitter();
    // Build container with required objects added to it.
    $container = new League\Container\Container();
    $container->share( 'request', $request );
    $container->share( 'response', $response );

    // Set up the auto injector. This is not a service locator!
    $injector = new Auryn\Injector();

    // Map interfaces to concrete implementation classes.
    $injector->alias( League\Container\ContainerInterface::class, League\Container\Container::class );
    $injector->alias( Psr\Http\Message\ResponseInterface::class, Zend\Diactoros\Response::class );
    $injector->alias( Psr\Http\Message\ServerRequestInterface::class, Zend\Diactoros\ServerRequest::class );
    $injector->alias( Ramsey\Uuid\UuidFactoryInterface::class, Ramsey\Uuid\UuidFactory::class );

    // TODO: Add loading of configuration file
    // TODO: Add setup of database connection / database abstraction layer

    // Set up objects which should be reused.
    $injector->share( $container );
    $injector->share( $request );
    $injector->share( $emitter );
    $injector->share( $injector );

    // Type hinting for auto completion in IDE.
    /** @var League\Route\RouteCollection $routeCollection */
    /** @var League\Route\Strategy\StrategyInterface $routeStrategy */

    // Create collection to add all routing in this API.
    $routeCollection = $injector->make( League\Route\RouteCollection::class );
    $routeStrategy = $injector->make( League\Route\Strategy\StrategyInterface::class );
    $routeCollection->setStrategy( $routeStrategy );

    try {
      $this->registerRoutePatterns( $routeCollection );
      $this->registerRoutes( $routeCollection, $injector );
      $response = $routeCollection->dispatch( $container->get( 'request' ), $container->get( 'response' ) );
    }
    catch( League\Route\Http\Exception\NotFoundException $e ){

      // TODO: Add exception handling for when the request matches no known routes

    }
    catch( \Exception $e ) {

      // TODO: Add exception handling for when an unexpected error occurs

    }
    finally {
      $emitter->emit( $response );
    }
  }

  /**
   * Registers custom patterns to match in the request urls.
   *
   * @param League\Route\RouteCollection $routeCollection
   * @return League\Route\RouteCollection
   */
  private function registerRoutePatterns( League\Route\RouteCollection $routeCollection ) {
    $routeCollection->addPatternMatcher( 'uuid', '[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}' );
    return $routeCollection;
  }

  /**
   * Registers the routes we wish to handle through controllers.
   *
   * @param League\Route\RouteCollection $routeCollection
   * @param Auryn\Injector $injector
   * @return League\Route\RouteCollection
   */
  private function registerRoutes( League\Route\RouteCollection $routeCollection, Auryn\Injector $injector ) {

    // TODO: Add application routes

    $routeCollection->get( '/', function( $request, $response, $arguments ){ return $response->withBody( 'Application index is running. :)' ); } );
    $routeCollection->get( '/someUuid:uuid', function( $request, $response, $arguments ){ return $response->withBody( 'Example with UUID in route: ' . $arguments[ 'someUuid' ] ); } );
    return $routeCollection;
  }

  /**
   * Create and initialize PSR-7 compliant Request object.
   *
   * @return Psr\Http\Message\ServerRequestInterface
   */
  private function buildRequest() {
    return Zend\Diactoros\ServerRequestFactory::fromGlobals(
        $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
    );
  }

  /**
   * Sets the auto loader.
   *
   * @param Composer\Autoload\ClassLoader $autoLoader
   */
  private function setAutoLoader( Composer\Autoload\ClassLoader $autoLoader ) {
    $this->autoLoader = $autoLoader;
  }

  /**
   * Returns the auto loader. This can be used to add extra PSR compatible
   * loading of classes by registering them on this ClassLoader.
   *
   * @return Composer\Autoload\ClassLoader
   */
  private function getAutoLoader() {
    return $this->autoLoader;
  }

}