<?php


$settings['hash_salt'] = 'Mn_9UTVaqvBxlCebt2fgYx7dTXKQEY4TDZcsQ-XYEoxVhuKKOg8ILkkNgFcfTlz8x4k5tOeg8g';

// Add database configurations here
$databases['default']['default'] = array (
  'database' => 'weshare',
  'username' => 'root',
  'password' => '',
  'prefix' => '',
  'host' => 'localhost',
  'port' => '3308',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings['file_temp_path'] = 'sites/default/files/tmp';
$settings['file_private_path'] = 'sites/default/files/private';

// $settings['update_free_access'] = TRUE;
ini_set('memory_limit', '-1');
$settings['trusted_host_patterns'] = array(
 '^weshare\.sj$',
 '^.+\.weshare\.sj$',
);

// $settings['container_yamls'][] = 'sites/default/services.yml';
// $config['system.file']['path']['temporary'] = 'sites/default/files/tmp';
// $config['preprocess_css'] = FALSE;
// $config['preprocess_js'] = FALSE;
// $config['system.performance']['css']['preprocess'] = 0;
// $config['system.performance']['js']['preprocess'] = 0;
// $config['advagg.settings']['enable'] = FALSE;
// $config['simplesamlphp_auth.settings']['activate'] = FALSE;
// $config['simplesamlphp_auth.settings']['basic']['activate'] = FALSE;
// $config['simplesamlphp_auth.settings']['allow']['default_login'] = FALSE;
