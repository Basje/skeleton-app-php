<?php namespace Basje;

class SkeletonApp {

  /**
   * @var array
   */
  private static $replacements = [];

  /**
   * Main function to execute after using `composer create-project`.
   */
  public static function postCreateProjectHook() {

    $projectName = self::getProjectName();
    self::registerReplacement( 'projectName', $projectName );

    echo wordwrap( 'You will need to choose a vendor name. Usually this is your ' .
         'company name. It will be used to set the root namespace in the PHP ' .
         'files and a lower case version will be used to set the vendor name ' .
         'in the composer.json file.', 75, PHP_EOL ),
          PHP_EOL, PHP_EOL;

    do {
      $vendorName = self::getUserInput( 'Please enter a vendor name: ' );
    }
    while( strlen( $vendorName ) === 0 );

    echo sprintf( 'You typed "%s"' . PHP_EOL, $vendorName );

    echo sprintf( 'Project name "%s" taken from directory name ' . PHP_EOL, $projectName );
    self::moveTemplateFiles();
    self::removeDirectoryTree( 'skel' );
  }

  /**
   * Determine a project name based on the path that Composer created.
   *
   * @return string Project name
   */
  private static function getProjectName() {
    return basename( realpath( "." ) );
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

  public static function getUserInput( $message ) {
    echo $message;
    return rtrim( fgets( STDIN ), PHP_EOL );
  }

}