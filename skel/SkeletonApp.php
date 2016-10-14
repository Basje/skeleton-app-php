<?php namespace Basje;

final class SkeletonApp {

  /**
   * @var array
   */
  private static $replacements = [];

  /**
   * Main function to execute after using `composer create-project`.
   */
  public static function postCreateProjectHook() {

    echo PHP_EOL, PHP_EOL;
    echo '  Welcome to the setup of your skeleton application. ', PHP_EOL,
         '  Please follow the instructions.', PHP_EOL, PHP_EOL;

    $composerPackageName = self::getComposerPackageName();
    $phpProjectNamespace = self::getPhpProjectNamespace();
    $phpProjectName = self::getPhpProjectNameFromNamespace( $phpProjectNamespace );

    self::registerReplacement( 'composerPackageName', $composerPackageName );
    self::registerReplacement( 'phpProjectName', $phpProjectName );

    self::setPlaceholdersInFile( implode( DIRECTORY_SEPARATOR, [ getcwd(), 'src', 'public', 'index.php' ] ) );

    // TODO: Write better functions for this functionality
//    self::moveTemplateFiles();
//    self::removeDirectoryTree( 'skel' );
  }

  /**
   * Register a replacement value for a placeholder.
   *
   * @param string $key   Placeholder name.
   * @param string $value Value to be used when placeholder is replaced.
   */
  private static function registerReplacement( $key, $value ) {
    $placeholder = sprintf( '{{ %s }}', trim( $key ) );
    self::$replacements[ $placeholder ] = $value;
  }

  /**
   * Move the skeleton template files to their destination.
   *
   * TODO: Split up this method in several others, it is doing to much.
   */
  private static function moveTemplateFiles() {
    $templatePath = 'skel/templates/';
    $templateSuffix = '-dist';
    $templateFilenamePattern = '{,.}*';
    $searchPattern = implode( '', [ $templatePath, $templateFilenamePattern, $templateSuffix ] );

    foreach( glob( $searchPattern, GLOB_BRACE ) as $templateFilename ) {
      $targetFileName = substr( $templateFilename, strlen( $templatePath ), strlen( $templateSuffix ) * -1 );
      echo sprintf( 'Creating clean file "%s" from template "%s"...' . PHP_EOL, $targetFileName, $templateFilename );
      copy( $templateFilename, $targetFileName );
      echo sprintf( 'Applying replacements to placeholders in "%s"...' . PHP_EOL, $templateFilename );
      self::setPlaceholdersInFile( $targetFileName );
    }
  }

  /**
   * Returns an array of file names from the current working directory and all
   * subdirectories. Optionally you can pass a subdirectory to start. The
   * returned file names are relative to the initial directory.
   *
   * @param string $directoryName Starting directory.
   * @return array Collection of file names.
   */
  private static function getFilesInDirectory( $directoryName = '.' ) {
    // Here we store our result.
    $files = [];
    // Strip whitespace, slashes, backslashes and dots from the start of the
    // string. Then prepend the current working directory to the value. This
    // is meant as a naive method to prevent people from requesting file
    // listings from outside this directory, either by accident or on purpose.
    $safeDirectoryName = ltrim( $directoryName . DIRECTORY_SEPARATOR, "./\\\n\r\t\0\x0B" );
    $fullDirectoryName = getcwd() . DIRECTORY_SEPARATOR . $safeDirectoryName;

    try {
      // Create an iterator which recursively traverses all directories and
      // iterates over all files and directories except dot files.
      $directoryIterator = new \RecursiveDirectoryIterator(
          $fullDirectoryName,
          \FilesystemIterator::NEW_CURRENT_AND_KEY | \FilesystemIterator::SKIP_DOTS
      );
      $iterator = new \RecursiveIteratorIterator(
          $directoryIterator,
          \RecursiveIteratorIterator::SELF_FIRST
      );
    }
    catch ( \Exception $e ) {
      // Some error occurred which we will ignore and just pretend that no
      // files have been found.
      return $files;
    }
    // Get all file names relative to the directory we started in.
    foreach( $iterator as $file ) {
      if( $file->isFile() ) {
        $files[] = substr( $file->getPathname(), strlen( $fullDirectoryName ) );
      }
    }
    return $files;
  }

  /**
   * Reads files, fills the placeholders with the actual values and writes the
   * new contents to disk.
   *
   * @param string $filename
   */
  private static function setPlaceholdersInFile( $filename ) {
    file_put_contents(
        $filename,
        strtr(
            file_get_contents( $filename ),
            self::$replacements
        )
    );
  }

  /**
   * Recursively removes a directory.
   *
   * @param string $directoryName
   * @return boolean
   */
  private static function removeDirectoryTree( $directoryName ) {
    $files = array_diff( scandir( $directoryName ), [ '.', '..' ] );
    foreach( $files as $fileName ) {
      $path = sprintf( '%s' . DIRECTORY_SEPARATOR . '%s', $directoryName, $fileName );
      is_dir( $path ) ? self::removeDirectoryTree( $path ) : unlink( $path );
    }
    return rmdir( $directoryName );
  }

  /**
   * Displays a message to the user and waits for an answer.
   *
   * @param string $message The message to prompt to the user.
   * @return string User input from standard in.
   */
  private static function getUserInput( $message ) {
    echo $message;
    return rtrim( fgets( STDIN ), PHP_EOL );
  }

  /**
   * Asks for the preferred Composer package name to be initialised on project
   * creation. Mirrors Composers' own pattern for the name for optimal
   * compatibility.
   *
   * @return string Composer package name.
   */
  private static function getComposerPackageName() {
    $packageNamePattern = '[a-z0-9_.-]+/[a-z0-9_.-]+';
    $message = 'Composer package name (<vendor>/<name>): ';
    $packageName = self::getUserInput( $message );
    while( preg_match( $packageNamePattern, $packageName ) !== 1 ) {
      echo 'The package name a is invalid, it should be lowercase and have a ',
           'vendor name, a forward slash, and a package name, matching: ',
           $packageNamePattern, PHP_EOL;
      $packageName = self::getUserInput( $message );
    }
    return $packageName;
  }

  /**
   * Asks for the preferred PHP project namespace to be initialised on project
   * creation.
   *
   * @return string PHP project namespace.
   */
  private static function getPhpProjectNamespace() {
    $namespacePattern = '[A-Za-z0-9_]+\[A-Za-z0-9_]+';
    $message = 'PHP project root namespace (<vendor>\<project>): ';
    $namespace = self::getUserInput( $message );
    while( preg_match( $namespacePattern, $namespace ) !== 1 ) {
      echo 'The PHP root namespace is invalid, it should have a ',
           'vendor name, a backslash, and a project name, matching: ',
           $namespacePattern, PHP_EOL;
      $namespace = self::getUserInput( $message );
    }
    return $namespace;
  }

  /**
   * @param string $namespace A PHP namespace.
   * @return string PHP project name.
   */
  private static function getPhpProjectNameFromNamespace( $namespace ) {
    list( $vendor, $projectName ) = explode( '\\', $namespace );
    return $projectName;
  }

}