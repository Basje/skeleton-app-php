<?php namespace {{ phpProjectNamespace }} {

  /*
   *  Boot the application.
   */

  // Construct location of autoload file, independent of OS.
  $autoLoaderFilename = implode(
      DIRECTORY_SEPARATOR,
      [
          dirname( dirname( __DIR__ ) ),
          'vendor',
          'autoload.php'
      ]
  );

  // Check if file is present on the system, otherwise stop application execution.
  if( ! file_exists( $autoLoaderFilename ) ) {
    die( 'Autoloader could not be included.' );
  }

  // Require the file and store the returned auto loader for later use.
  $autoLoader = require_once $autoLoaderFilename;

  // Create the portal main application and run it.
  $application = new Application( $autoLoader );
  $application->run();

}