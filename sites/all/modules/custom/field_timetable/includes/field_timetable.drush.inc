<?php

/**
 * @file
 * Drush integration for Field Timetable.
 */

/**
 * The Field Timetable plugin URI.
 */
define('TIMETABLE_VERSION', 'targoo-timetable-2a9d18c');
define('TIMETABLE_DOWNLOAD_URI', 'https://github.com/targoo/timetable/zipball/master');

/**
 * Implements hook_drush_command().
 */
function field_timetable_drush_command() {
  $items = array();

  // The key in the $items array is the name of the command.
  $items['field_timetable-plugin'] = array(
    'description' => dt("Downloads the Timetable plugin."),
    'arguments' => array(
      'path' => dt('Optional. A path where to install the Timetable plugin. If omitted Drush will use the default location.'),
    ),
  );

  return $items;
}

/**
 * Implements hook_drush_help().
 */
function field_timetable_drush_help($section) {
  switch ($section) {
    case 'drush:field_timetable-plugin':
      return dt("Downloads the Timetable plugin from github, default location is sites/all/libraries.");
  }
}

/**
 * Commands to download the Field Timetable plugin.
 */
function drush_field_timetable_plugin($path = 'sites/all/libraries') {
  if (!drush_shell_exec('type unzip')) {
    return drush_set_error(dt('Missing dependency: unzip. Install it before using this command.'));
  }

  // Create the path if it does not exist.
  if (!is_dir($path)) {
    drush_op('mkdir', $path);
    drush_log(dt('Directory @path was created', array('@path' => $path)), 'notice');
  }

  // Set the directory to the download location.
  $olddir = getcwd();
  chdir($path);

  $filename = 'master';
  //return drush_set_error(dt($filename));
  
  $dirname = 'timetable';
  //return drush_set_error(dt($dirname));
    
  // Remove any existing Field Timetable plugin directory.
  if (is_dir($dirname)) {
    drush_log(dt('A existing Field Timetable plugin was overwritten at @path', array('@path' => $path)), 'notice');
  }
  // Remove any existing Field Timetable plugin zip archive.
  if (is_file($filename)) {
    drush_op('unlink', $filename);
  }

  // Download the zip archive.
  if (!drush_shell_exec('wget ' . TIMETABLE_DOWNLOAD_URI)) {
    drush_shell_exec('curl -kOL ' . TIMETABLE_DOWNLOAD_URI);
  }
  
  if (is_file($filename)) {
    // Decompress the zip archive.
    drush_shell_exec('unzip -qq -o ' . $filename);
    // Remove the zip archive.
    drush_op('unlink', $filename);
    // Remove old librairy.
    if (is_dir('timetable')) {
      drush_shell_exec('rm -rf timetable');
    }
    
    drush_shell_exec('mv targoo-timetable-2a9d18c timetable');
    drush_shell_exec('rm -rf targoo-timetable-2a9d18c');
  }

  // Set working directory back to the previous working directory.
  chdir($olddir);

  if (is_dir($path . '/timetable')) {
    drush_log(dt('Timetable plugin has been downloaded to @path', array('@path' => $path)), 'success');
  }
  else {
    drush_log(dt('Drush was unable to download the Timetable plugin to @path', array('@path' => $path)), 'error');
  }
}
