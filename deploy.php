<?php

/*
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Task\Context;

require 'recipe/common.php';

/**
 * Server Configuration
 */

// Define servers
inventory('servers.yml');

// Default server
set('default_stage', 'production');

// nginx user
set('http_user', 'wordpress');

// nginx group
set('http_group', 'wordpress');

// Temporary directory path
set('tmp_path', '/tmp/deployer');


/**
 * Bedrock Configuration
 */

// Bedrock project repository
set('repository', 'git@github.com:carlalexander/carlalexander.ca.git');

// Bedrock shared files
set('shared_files', ['.env']);

// Bedrock shared directories
set('shared_dirs', ['web/app/uploads']);

// Bedrock writable directories
set('writable_dirs', ['web/app/uploads']);


/**
 * Load environment variables
 */
task('load-environment-variables', function () {
    $host = Context::get()->getHost();

    if (empty($host->getUser()) && getenv('DEP_USER')) {
        $host->user((string) getenv('DEP_USER'));
    }
});

/**
 * Backup all shared files and directories
 */
task('setup:backup', function () {
    $currentPath = '{{deploy_path}}/current';
    $tmpPath = get('tmp_path');

    // Delete tmp dir if it exists.
    run("if [ -d $tmpPath ]; then rm -R $tmpPath; fi");

    // Create tmp dir.
    run("mkdir -p $tmpPath");

    foreach (get('shared_dirs') as $dir) {
        // Check if the shared dir exists.
        if (test("[ -d $(echo $currentPath/$dir) ]")) {
            // Create tmp shared dir.
            run("mkdir -p $tmpPath/$dir");

            // Copy shared dir to tmp shared dir.
            run("cp -rv $currentPath/$dir $tmpPath/" . dirname($dir));
        }
    }

    foreach (get('shared_files') as $file) {
        // If shared file exists, copy it to tmp dir.
        run("if [ -f $(echo $currentPath/$file) ]; then cp $currentPath/$file $tmpPath/$file; fi");
    }
})->desc('Backup all shared files and directories');


/**
 * Purge all files from the deploy path directory
 */
task('setup:purge', function () {
    // Delete everything in deploy dir.
    run('rm -R {{deploy_path}}/*');
})->desc('Purge all files from the deploy path directory');


/**
 * Restore backup of shared files and directories
 */
task('setup:restore', function() {
    $sharedPath = "{{deploy_path}}/shared";
    $tmpPath = get('tmp_path');

    foreach (get('shared_dirs') as $dir) {
        // Create shared dir if it does not exist.
        if (!test("[ -d $sharedPath/$dir ]")) {
            // Create shared dir if it does not exist.
            run("mkdir -p $sharedPath/$dir");
        }

        // If tmp shared dir exists, copy it to shared dir.
        run("if [ -d $(echo $tmpPath/$dir) ]; then cp -rv $tmpPath/$dir $sharedPath/" . dirname($dir) . "; fi");
    }

    foreach (get('shared_files') as $file) {
        // If tmp shared file exists, copy it to shared dir.
        run("if [ -f $(echo $tmpPath/$file) ]; then cp $tmpPath/$file $sharedPath/$file; fi");
    }
})->desc('Restore backup of shared files and directories');


/**
 * Configure known_hosts for git repository
 */
task('setup:known_hosts', function () {
    $repository = get('repository');
    $host = '';

    if (filter_var($repository, FILTER_VALIDATE_URL) !== FALSE) {
        $host = parse_url($repository, PHP_URL_HOST);
    } elseif (preg_match('/^git@(?P<host>\w+?\.\w+?):/i', $repository, $matches)) {
        $host = $matches['host'];
    }

    if (empty($host)) {
        throw new \RuntimeException('Couldn\'t parse host from repository.');
    }

    run("ssh-keyscan -H -T 10 $host >> ~/.ssh/known_hosts");
})->desc('Configure known_hosts for git repository');


/**
 * Setup success message
 */
task('setup:success', function () {
    Deployer::setDefault('terminate_message', '<info>Successfully setup!</info>');
})->local()->setPrivate();


/**
 * Reload php-fpm service
 */
task('php-fpm:reload', function () {
    run('sudo /etc/init.d/php7.0-fpm reload');
})->desc('Reload php-fpm service');


/**
 * Reload varnish service
 */
task('varnish:reload', function () {
    run('sudo /etc/init.d/varnish reload');
})->desc('Reload varnish service');


/**
 * Deploy task
 */
task('deploy', [
    'load-environment-variables',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'varnish:reload',
    'php-fpm:reload',
])->desc('Deploy your Bedrock project');
after('deploy', 'success');


/**
 * Setup task
 */
task('setup', [
    'setup:backup',
    'setup:purge',
    'deploy:prepare',
    'setup:restore',
    'setup:known_hosts',
])->desc('Setup your Bedrock project');
after('setup', 'setup:success');
