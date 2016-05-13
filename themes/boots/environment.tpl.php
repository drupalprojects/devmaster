<div class="list-group environment <?php print $environment->class  ?>" id="<?php print $environment->project_name; ?>-<?php print $environment->name ?>">

    <?php if (!empty($environment->task_links) && !isset($page)): ?>
    <!-- Environment Settings & Task Links -->
    <div class="environment-dropdowns">
        <div class="environment-tasks btn-group ">
            <button type="button" class="btn btn-link task-list-button dropdown-toggle" data-toggle="dropdown" title="<?php print t('Environment Settings & Actions') ;?>">
                <i class="fa fa-sliders"></i>
            </button>
            <?php print $environment->task_links_rendered; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="environment-header list-group-item list-group-item-<?php print $environment->list_item_class ?>">

      <?php  if (isset($environment->github_pull_request)): ?>
        <img src="<?php print $environment->github_pull_request->pull_request_object->user->avatar_url ?>" width="32" height="32" class="environment-avatar">
      <?php endif; ?>

      <!-- Environment Name -->
        <a href="<?php print $environment->site? url("node/$environment->site"): url("node/$environment->platform"); ?>" class="environment-link" title="<?php print t('Environment: ') . $environment->name; ?>">
            <?php print $environment->name; ?></a>

      <?php
      // If we detect a currently running deploy...
      if (isset($environment->tasks['devshop-deploy'])):
        $task = current($environment->tasks['devshop-deploy']);

        if (($environment->git_ref != $task->task_args['git_ref'] || $environment->git_ref != $environment->git_ref_stored) && ($task->task_status == HOSTING_TASK_QUEUED || $task->task_status == HOSTING_TASK_PROCESSING)): ?>
        <span title="<?php print t('Deploying from @source to @target...', array('@source' => $environment->git_ref, '@target' => $task->task_args['git_ref'])); ?>">
          <a href="<?php print $environment->git_ref_url; ?>" class="environment-meta-data environment-git-ref btn btn-text" target="_blank"  title="<?php print t('Current actual git ref.'); ?>">
            <i class='fa fa-<?php print $environment->git_ref_type == 'tag'? 'tag': 'code-fork'; ?>'></i><?php print (!empty($environment->git_ref_stored)? $environment->git_ref_stored: $environment->git_ref); ?>
          </a>
          <i class="fa fa-caret-right text-muted small"></i>
          <a href="<?php print $environment->git_ref_url; ?>" class="environment-meta-data environment-git-ref btn btn-text" target="_blank"  title="<?php print t('Desired git ref.'); ?>">
          <i class='fa fa-<?php print $project->settings->git['refs'][$task->task_args['git_ref']] == 'tag'? 'tag': 'code-fork'; ?>'></i><?php print $task->task_args['git_ref']; ?>
          </a>
          </span>

          <?php else: ?>
            <a href="<?php print $environment->git_ref_url; ?>" class="environment-meta-data environment-git-ref btn btn-text" target="_blank" title="<?php print t('Git !type: ', array('!type' => $environment->git_ref_type)) . $environment->git_ref; ?>">
            <i class='fa fa-<?php print $environment->git_ref_type == 'tag'? 'tag': 'code-fork'; ?>'></i><?php print $environment->git_ref; ?>
          </a>
        <?php endif; ?>

      <?php else: ?>
        <a href="<?php print $environment->git_ref_url; ?>" class="environment-meta-data environment-git-ref btn btn-text" target="_blank" title="<?php print t('Git !type: ', array('!type' => $environment->git_ref_type)) . $environment->git_ref; ?>">
          <i class='fa fa-<?php print $environment->git_ref_type == 'tag'? 'tag': 'code-fork'; ?>'></i><?php print $environment->git_ref; ?>
        </a>
      <?php endif; ?>


        <?php if ($environment->version): ?>
            <a href="<?php print url("node/$environment->platform"); ?>"  title="Drupal version <?php print $environment->version; ?>" class="environment-meta-data environment-drupal-version btn btn-text">
                <i class="fa fa-drupal"></i><?php print $environment->version; ?>
            </a>
        <?php endif; ?>

        <?php if ($environment->site_status == HOSTING_SITE_DISABLED): ?>
            <span class="environment-meta-data">Disabled</span>
        <?php endif; ?>

        <!-- Environment Status Indicators -->
        <div class="environment-indicators">
            <?php if (isset($environment->settings->locked) && $environment->settings->locked): ?>
                <span class="environment-meta-data text-muted" title="<?php print t('This database is locked.'); ?>">
              <i class="fa fa-lock"></i><?php print t('Locked') ?>
            </span>
            <?php endif; ?>

            <?php if ($environment->name == $project->settings->live['live_environment']): ?>
                <span class="environment-meta-data text-muted" title="<?php print t('This is the live environment.'); ?>">
            <i class="fa fa-bolt"></i>Live
          </span>
            <?php endif; ?>

        </div>

      <?php  if (isset($environment->github_pull_request)): ?>
        <!-- Pull Request -->


        <div class="environment-pull-request">
          <a href="<?php print $environment->github_pull_request->pull_request_object->html_url ?>" class="pull-request" target="_blank">
            <i class="fa fa-github"></i>
            <?php print t('PR') . ' ' . $environment->github_pull_request->number ?>:
            <?php print $environment->github_pull_request->pull_request_object->title;?>
          </a>
        </div>

      <?php endif; ?>

    </div>

    <!-- Environment Warnings -->
    <?php if (!empty($warnings)): ?>
        <?php foreach ($warnings as $warning):

            if ($warning['type'] == 'warning') {
                $icon = 'exclamation-triangle';
                $class = 'warning';
            }
            elseif ($warning['type'] == 'error') {
                $icon = 'exclamation-circle';
                $class = 'danger';
            }
            ?>
        <div class="list-group-item list-group-item-<?php print $class ?> text">
            <i class="fa fa-<?php print $icon ?>"></i>
            <?php print $warning['text'] ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php

      // SITUATION: Environment Destroy Initiated
      if (!empty($environment->tasks['delete'])): ?>
      <!-- Status Display -->
      <?php

      foreach ($environment->tasks['delete'] as $task) {
        if ($environment->site == $task->rid) {
          $site_delete_task = $task;
          $site_delete_status = l($site_delete_task->status_name, "node/{$site_delete_task->nid}");
        }
        elseif ($environment->platform == $task->rid) {
          $platform_delete_task = $task;
          $platform_delete_status = l($platform_delete_task->status_name, "node/{$platform_delete_task->nid}");
        }
      }

      ?>

      <?php if (isset($site_delete_task)): ?>
        <div class="list-group-item center-block text text-muted">
          <i class="fa fa-trash"></i>
          <?php print t('Site Destroy'); ?>: <?php print $site_delete_status; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($platform_delete_task)): ?>
      <div class="list-group-item center-block text text-muted">
        <i class="fa fa-trash"></i>
        <?php print t('Platform Destroy'); ?>: <?php print $platform_delete_status; ?>
      </div>
      <?php endif; ?>

    <?php

      // SITUATION: Clone Failure
      elseif ($environment->created['type'] == 'clone' && !empty($environment->tasks['delete']) ||
        empty($environment->site) && !empty($environment->platform) && !empty($environment->tasks['clone']) && current($environment->tasks['clone'])->task_status == HOSTING_TASK_ERROR
      ): ?>
        <!-- Status Display -->
        <div class="list-group-item center-block text text-muted">

            <?php if ($environment->created['status'] == HOSTING_TASK_ERROR): ?>
            <i class="fa fa-warning"></i> <?php print t('Environment clone failed.'); ?>
            <?php endif ;?>

        </div>
        <div class="list-group-item">
            <div class="btn-group" role="group">
                <a href="<?php print url("node/{$environment->created['nid']}"); ?>" class="btn btn-default">
                    <i class="fa fa-list"></i> <?php print t('View Logs'); ?>
                </a>
                <?php if (empty($environment->site) && $environment->platform): ?>
                    <a href="<?php print url("hosting_confirm/{$environment->platform}/platform_delete", array('query' => array('token' => $token))); ?>" class="btn btn-danger">
                        <i class="fa fa-trash"></i> <?php print t('Destroy Environment'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php
      // SITUATION: Environment has platform but no site, and verify is queued or processing
      elseif (empty($environment->site) && !empty($environment->platform) && !empty($environment->tasks['verify']) && (current($environment->tasks['verify'])->task_status == HOSTING_TASK_QUEUED || current($environment->tasks['verify'])->task_status == HOSTING_TASK_PROCESSING)): ?>
        <div class="list-group-item center-block text text-muted">
          <i class="fa fa-truck"></i>
          <?php print t('Environment is being created.'); ?>
        </div>

    <?php
      // SITUATION: Environment has platform but no site, and clone is queued or processing
      elseif (empty($environment->site) && !empty($environment->platform) && !empty($environment->tasks['clone']) && (current($environment->tasks['clone'])->task_status == HOSTING_TASK_QUEUED || current($environment->tasks['clone'])->task_status == HOSTING_TASK_PROCESSING)): ?>
        <div class="list-group-item center-block text text-muted">
          <i class="fa fa-truck"></i>
          <?php print t('Environment is being created.'); ?>
        </div>

    <?php
      // SITUATION: Environment has platform but no site, verify failed
      elseif (empty($environment->site) && !empty($environment->platform) && !empty($environment->tasks['verify']) && current($environment->tasks['verify'])->task_status == HOSTING_TASK_ERROR):

        $verify_task = current($environment->tasks['verify']);
        ?>
        <div class="list-group-item center-block text text-muted">

          <i class="fa fa-warning"></i>
          <?php print t('Codebase preparation failed.'); ?>
        </div>

        <div class="list-group-item center-block text text-muted">
          <div class="btn-group " role="group">
            <a href="<?php print url("node/{$verify_task->nid}"); ?>" class="btn btn-default">
              <i class="fa fa-refresh"></i> <?php print t('View the Logs and Retry'); ?>
            </a>
          </div>
        </div>

    <?php
      // SITUATION: Environment has platform but no site, verify succeeded, and there is NOT a clone task...
      elseif (empty($environment->site) && !empty($environment->platform) && !empty($environment->tasks['verify']) && empty($environment->tasks['clone']) && (current($environment->tasks['verify'])->task_status == HOSTING_TASK_SUCCESS || current($environment->tasks['verify'])->task_status == HOSTING_TASK_WARNING)):

        $verify_task = current($environment->tasks['verify']);
        ?>
        <div class="list-group-item center-block text text-muted">

          <i class="fa fa-warning"></i>
          <?php print t('Aegir Platform has been created, but Site is missing. Please contact your administrator.'); ?>
        </div>

        <div class="list-group-item center-block text text-muted">
          <div class="btn-group " role="group">
            <a href="<?php print url("node/{$verify_task->nid}"); ?>" class="btn btn-default">
              <i class="fa fa-refresh"></i> <?php print t('View the Logs and Retry'); ?>
            </a>
          </div>
        </div>

    <?php
      // SITUATION: Site Install Failed
      elseif ($environment->created['type'] == 'install' && $environment->created['status'] == HOSTING_TASK_ERROR): ?>

        <div class="list-group-item center-block text text-muted">
            <i class="fa fa-warning"></i>  <?php print t('Site Install failed. The environment is not available.'); ?>
        </div>
        <div class="list-group-item center-block text text-muted">
            <div class="btn-group " role="group">
                <a href="<?php print url("node/{$environment->created['nid']}"); ?>" class="btn btn-default">
                    <i class="fa fa-refresh"></i> <?php print t('View the Logs and Retry'); ?>
                </a>
                <?php if (variable_get('hosting_require_disable_before_delete', TRUE) && $environment->site_status != HOSTING_SITE_DISABLED): ?>
                <a href="<?php print url("hosting_confirm/{$environment->site}/site_disable", array('query' => array('token' => $token))); ?>" class="btn btn-danger">
                    <i class="fa fa-power-off"></i> <?php print t('Disable the Environment'); ?>
                </a>
                <?php else: ?>
                    <a href="<?php print url("hosting_confirm/{$environment->site}/site_delete", array('query' => array('token' => $token))); ?>" class="btn btn-danger">
                        <i class="fa fa-trash"></i> <?php print t('Destroy the Environment'); ?>
                    </a>
                <?php endif; ?>

            </div>
        </div>

    <?php
      // SITUATION: Site Install Queued or processing.
      elseif ($environment->created['type'] == 'install' && $environment->created['status'] == HOSTING_TASK_QUEUED || $environment->created['status'] == HOSTING_TASK_PROCESSING): ?>

        <div class="list-group-item center-block text text-muted">
          <i class="fa fa-truck"></i>
          <?php print t('Environment install in progress.'); ?>
        </div>

        <?php
      // SITUATION: Environment Disable Initiated
      elseif (!empty($environment->tasks['disable']) && (current($environment->tasks['disable'])->task_status == HOSTING_TASK_QUEUED || current($environment->tasks['disable'])->task_status == HOSTING_TASK_PROCESSING)): ?>
        <div class="list-group-item center-block text text-muted">
          <i class="fa fa-power-off"></i>
          <?php print t('Environment is being disabled.'); ?>
        </div>
        <?php

      // SITUATION: Site is Disabled
      elseif ($environment->site_status == HOSTING_SITE_DISABLED): ?>
        <div class="list-group-item center-block text text-muted">
          <i class="fa fa-power-off"></i>
          <?php print t('Environment is disabled.'); ?>
        </div>

        <div class="list-group-item center-block text text-muted">
          <div class="btn-group">
            <a href="<?php print url("hosting_confirm/{$environment->site}/site_enable", array('query' => array('token' => $token))); ?>" class="btn btn-lg">
              <i class="fa fa-power-off"></i> <?php print t('Enable'); ?>
            </a>
            <a href="<?php print url("hosting_confirm/{$environment->site}/site_delete", array('query' => array('token' => $token))); ?>" class="btn btn-lg">
              <i class="fa fa-trash"></i> <?php print t('Destroy'); ?>
            </a>
          </div>
        </div>

    <?php
      // SITUATION: Environment is Active!
      elseif (empty($environment->tasks['delete'])): ?>

        <!-- URLs -->
        <div class="environment-domains list-group-item <?php if ($environment->login_text) print 'login-available'; ?>">

            <div class="btn-toolbar" role="toolbar">

                <?php
                // If we have more than one domain, add the dropdown.
                if (count($environment->domains) > 1):
                    ?>
                    <div class="btn-group btn-group-smaller btn-urls" role="group">
                        <a href="<?php print $environment->url ?>" target="_blank" class="environment-link">
                            <?php if (!empty($environment->ssl_enabled)): ?>
                                <i class="fa fa-lock text-success"></i>
                            <?php else: ?>
                                <i class="fa fa-globe"></i>
                            <?php endif; ?>
                            <span class="hidden">Visit Environment:</span>
                            <?php print $environment->url ?>
                        </a>
                    </div>
                    <div class="btn-group btn-group-smaller" role="group">
                        <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-globe"></i>
                            <?php print count($environment->domains); ?>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <?php foreach ($environment->domains as $domain): ?>
                                <li><a href="<?php print 'http://' . $domain; ?>" target="_blank"><?php print 'http://' . $domain; ?></a></li>
                            <?php endforeach; ?>
                            <li class="divider">&nbsp;</li>
                            <li><?php print l(t('Edit Domains'), 'node/' . $node->nid . '/edit/' . $environment->name, array('query'=> drupal_get_destination())); ?></li>
                        </ul>
                    </div>

                    <?php
                // If site only has one domain (no aliases):
                else: ?>

                    <?php if (!empty($environment->url)): ?>
                        <div class="btn-group btn-group-smaller btn-urls-single" role="group">
                            <a href="<?php print $environment->url ?>" target="_blank">
                                <?php if (!empty($environment->ssl_enabled)): ?>
                                    <i class="fa fa-lock" alt="<?php print t('Encrypted'); ?>"></i>
                                <?php else: ?>
                                    <i class="fa fa-globe"></i>
                                <?php endif;?>
                                <?php print $environment->url ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <button class="btn btn-xs">
                            <i class="fa fa-globe"></i>
                            <em>&nbsp;</em>
                        </button>
                    <?php endif;?>

                <?php endif;?>

                <!-- Log In Link -->
                <?php if ($environment->login_text): ?>
                    <div class="btn-group btn-group-smaller pull-right login-link" role="group">

                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-link" data-toggle="modal" data-target="#loginModal-<?php print $environment->name ?>" data-remote="<?php print url('devshop/login/reset/' . $environment->site); ?>">
                                <i class="fa fa-sign-in"></i>
                                <?php print $environment->login_text; ?>
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="loginModal-<?php print $environment->name ?>" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel-<?php print $environment->name ?>">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="loginModalLabel-<?php print $environment->name ?>">
                                                <?php print $environment->login_text; ?>
                                            </h4>
                                        </div>
                                        <div class="modal-body">
                                            <i class="fa fa-gear fa-spin"></i>
                                            <?php print t('Requesting new log in link. Please wait...') ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php print t('Cancel'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                <?php endif;?>
            </div>
        </div>

        <div class="list-group-item">
          <label><?php print t('Browse'); ?></label>
          <div class="btn-group" role="group">

            <?php if (drupal_valid_path("node/{$environment->site}/errors")): ?>
            <!-- Browse Logs -->
            <a href="<?php print url("node/$environment->site/errors"); ?>" class="btn btn-text text-muted small" title="<?php print t('Error logs for this environment.'); ?>">
              <i class="fa fa-tasks"></i>
              <?php print t('Logs'); ?>
            </a>
            <?php endif; ?>

            <!-- Browse Files -->
            <a href="<?php print url("node/$environment->site/files/platform"); ?>" class="btn btn-text text-muted small" title="<?php print t('Browse the files in this environment'); ?>">
              <i class="fa fa-folder-o"></i>
              <?php print t('Files'); ?>
            </a>

            <!-- Browse Backups -->
            <a href="<?php print url("node/$environment->site/backups"); ?>" class="btn btn-text text-muted small" title="<?php print t('Create a view backups.'); ?>">
              <i class="fa fa-database"></i>
              <?php print t('Backups'); ?>
            </a>
          </div>
        </div>

        <?php
        // Only show this area if they have at least one of these permissions.
        if (
                user_access('create devshop-deploy task') ||
                user_access('create sync task') ||
                user_access('create migrate task')
        ): ?>
            <div class="environment-deploy list-group-item">

                <!-- Deploy -->
                <label><?php print t('Deploy'); ?></label>
                <div class="btn-group btn-toolbar" role="toolbar">

                    <?php if (user_access('create devshop-deploy task')): ?>
                        <!-- Deploy: Code -->
                        <div class="btn-group btn-deploy-code" role="group">
                            <button type="button" class="btn btn-default dropdown-toggle btn-git-ref" data-toggle="dropdown"><i class="fa fa-code"></i>
                                <?php print t('Code'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu btn-git-ref" role="menu">
                                <li><label><?php print t('Deploy branch or tag'); ?></label></li>
                                <?php if (count($git_refs)): ?>
                                    <?php foreach ($git_refs as $ref => $item): ?>
                                        <li>
                                            <?php print str_replace('ENV_NID', $environment->site, $item); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (user_access('create sync task')): ?>
                        <!-- Deploy: Data -->
                        <div class="btn-group btn-deploy-database" role="group">

                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-database"></i>
                                <?php print t('Data'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <?php if (isset($environment->settings->locked) && $environment->settings->locked): ?>
                                    <li><label><?php print t('This environment is locked. You cannot deploy data here.'); ?></label></li>
                                <?php elseif (count($target_environments) == 1): ?>
                                    <li><label><?php print t('No other environments available.'); ?></label></li>
                                <?php else: ?>
                                    <li><label><?php print t('Deploy data from'); ?></label></li>
                                    <?php foreach ($source_environments as $source): ?>
                                        <?php if ($source->name == $environment->name) continue; ?>
                                        <li><a href="/hosting_confirm/<?php print $environment->site ?>/site_sync/?source=<?php print $source->site ?>">
                                                <?php if ($project->settings->live['live_environment'] == $source->name): ?>
                                                    <i class="fa fa-bolt deploy-db-indicator"></i>
                                                <?php elseif (isset($source->settings->locked) && $source->settings->locked): ?>
                                                    <i class="fa fa-lock deploy-db-indicator"></i>
                                                <?php endif; ?>

                                                <strong class="btn-block"><?php print $source->name ?></strong>
                                                <small><?php print $source->url; ?></small>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                    <?php if ($project->settings->deploy['allow_deploy_data_from_alias']): ?>
                                        <li class="divider"></li>
                                        <li><a href="/hosting_confirm/<?php print $environment->site ?>/site_sync/?source=other&dest=<?php print $source->name ?>">
                                                <strong class="btn-block"><?php print t('Other...'); ?></strong>
                                                <small><?php print t('Enter a drush alias to deploy from.'); ?></small>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>


                    <?php if (user_access('create migrate task')): ?>
                        <!-- Deploy: Stack -->
                        <div class="btn-group btn-deploy-servers" role="group">

                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i>
                                <?php print t('Stack'); ?>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu devshop-stack" role="menu">
                                <li><label><?php print t('IP Address'); ?></label></li>
                                <?php foreach ($environment->ip_addresses as $ip): ?>
                                    <li class="text">
                                        <?php print $ip ?>
                                    </li>
                                <?php endforeach; ?>

                                <li><label><?php print t('Servers'); ?></label></li>
                                <?php foreach ($environment->servers as $type => $server):
                                    // DB: Migrate Task
                                    if ($type == 'db') {
                                        $icon = 'database';
                                        $url = "hosting_confirm/{$environment->site}/site_migrate";
                                    }
                                    // HTTP: Edit Platform
                                    elseif ($type == 'http') {
                                        $icon = 'cube';
                                        $url = "node/{$environment->platform}/edit";
                                    }
                                    // SOLR: Edit Site
                                    elseif ($type == 'solr') {
                                        $icon = 'sun-o';
                                        $url = "node/{$environment->project_nid}/edit/{$environment->name}";
                                    }

                                    // Build http query.
                                    $query = array();
                                    $query['destination'] = $_GET['q'];
                                    $query['deploy'] = 'stack';

                                    $full_url = url($url, array('query' => $query));

                                    // @TODO: Not sure why nid is localhost here.
                                    $server_url = $server['nid'] == 'localhost'?
                                            'server_localhost':
                                            url('node/' . $server['nid']);
                                    ?>
                                    <li class="inline">
                                        <a href="<?php print $server_url; ?>" title="<?php print $type .': ' . $server['name']; ?>">
                                            <strong class="btn-block"><i class="fa fa-<?php print $icon; ?>"></i> <?php print $type; ?></strong>
                                            <small><?php print $server['name']; ?></small>
                                        </a>
                                        <?php if ($full_url) :?>
                                            <a href="<?php print $full_url;?>" title="<?php print t('Change !type server...', array('!type' => $type)); ?>"><i class="fa fa-angle-right"></i></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php if (isset($environment->settings->deploy) && count(array_filter($environment->settings->deploy)) > 0): ?>
    <div class="list-group-item environment-dothooks">
        <label title="<?php print t('These hooks will run on every automatic deploy.');?>"><?php print t('Hooks'); ?></label>
        <div class="btn-group btn-hooks" role="group">
            <?php
            /**
             * @TODO:
             * - Move this to a preprocessor.
             * - Make a hook_devshop_hook_types() hook so other modules can expand on deploy hooks.
             */ ?>
            <?php foreach ($environment->settings->deploy as $hook_type => $enabled): ?>
                <?php if ($enabled): ?>
                    <div class="btn-group btn-hook-" role="group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <?php
                            if ($hook_type == 'dothooks') {
                                $hook_type_title = t('Hooks YML');
                            }
                            elseif ($hook_type == 'acquia_hooks') {
                                $hook_type_title = t('Acquia Cloud Hooks');
                            }
                            else {
                                $hook_type_title = $hook_type;
                            }
                            ?>

                            <?php print $hook_type_title ; ?>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <?php if ($hook_type == 'cache'): ?>
                                <li><label><?php print t('Clear Caches'); ?></label></li>
                                <li class="text">
                                    <p class="text-info">
                                        <i class="fa fa-question-circle"></i>
                                        <?php print t("All Drupal caches are cleared every time code is deployed."); ?>
                                    </p>
                                </li>
                                <li>
                                    <pre>drush clear-cache all</pre>
                                </li>
                            <?php elseif ($hook_type == 'update'): ?>
                                <li><label><?php print t('Run Database Updates'); ?></label></li>
                                <li class="text">
                                    <p class="text-info">
                                        <i class="fa fa-question-circle"></i>
                                        <?php print t("Drupal database updates are run every time new code is deployed."); ?>
                                    </p>
                                </li>
                                <li>
                                    <pre>drush update-db -y</pre>
                                </li>
                            <?php elseif ($hook_type == 'revert'): ?>
                                <li><label><?php print t('Revert all features'); ?></label></li>
                                <li class="text">
                                    <p class="text-info">
                                        <i class="fa fa-question-circle"></i>
                                          <?php print t("All features modules are reverted every time new code is deployed."); ?>
                                    </p>
                                </li>
                                <li>
                                    <pre>drush features-revert-all -y</pre>
                                </li>
                            <?php elseif ($hook_type == 'dothooks'): ?>
                                <li><label><?php print t('File-based Hooks'); ?></label></li>
                                <li class="text"><p class="text-info">
                                        <i class="fa fa-question-circle"></i>
                                        <?php print t("When code is deployed, the scripts in the 'deploy' section of a .hooks or .hooks.yml file in your project is run."); ?></p></li>

                              <?php if (!empty($environment->dothooks_file_name)): ?>
                              <li class="text"><p class=\"text-info\">
                                        <i class=\"fa fa-question-circle\"></i>
                                        <?php print t("This is your &filename:", array(
                                          '&filename' => $environment->dothooks_file_name
                                        )); ?></p></li>
                                <li>
                                    <pre><?php if (isset($environment->dothooks_file_path)) { print file_get_contents($environment->dothooks_file_path); } ?></pre>
                                </li>

                                <?php endif; ?>
                            <?php elseif ($hook_type == 'acquia_hooks'): ?>
                                <li><label><?php print t('Acquia Cloud Hooks'); ?></label></li>
                                <li class="text"><p class="text-info">
                                        <i class="fa fa-question-circle"></i>
                                        <?php print t("When code or data is deployed, the appropriate Acquia Cloud Hook within the project will be triggered."); ?></p></li>
                                <li class="text"><p class="text-muted"><?php print t('See !link1 and !link2 for more information ', array(
                                        '!link1' => l('Acquia Cloud Documentation', 'https://docs.acquia.com/cloud/manage/cloud-hooks'),
                                        '!link2' => l('https://github.com/acquia/cloud-hooks', 'https://github.com/acquia/cloud-hooks'),
                                        )); ?></p>
                                </li>
                                <li>
                                    <label>Supported Cloud Hooks</label>
                                </li>
                                <li class="text">

                                    <ul>
                                        <li><strong>post-code-deploy:</strong> <?php print t('Triggered after a <em>manually</em> started "Deploy Code" task ends.'); ?></li>
                                        <li><strong>post-code-update:</strong> <?php print t('Triggered after an <em>automatic</em> "Deploy Code" task ends. (When developers "git push")'); ?></li>
                                        <li><strong>post-db-copy:</strong> <?php print t('Triggered after a "Deploy Data" task runs if "Database" was selected.'); ?></li>
                                        <li><strong>post-files-copy:</strong> <?php print t('Triggered after a "Deploy Data" task runs if "Database" was selected.'); ?></li>
                                    </ul>

                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

  <?php if ($environment->git_sha): ?>

    <?php
    // Figure out status
    $item_class = 'default';
    $icon = 'check';
    $label = t('Clean');
    $node = '';

    if (strpos($environment->git_status, 'Your branch is ahead') !== FALSE) {
      $icon = 'arrow-right';
      $label = t('Ahead');
      $item_class = 'info';
    }

    if (strpos($environment->git_status, 'Untracked files:') !== FALSE) {
      $icon = 'exclamation-circle';
      $label = t('Untracked Files');
      $item_class = 'warning';

      // Detect Aegir files we should be ignoring.
      if (strpos($environment->git_status, 'sites/sites.php') !== FALSE || strpos($environment->git_status, 'sites/' . $environment->uri) !== FALSE || strpos($environment->git_status, 'sites/all/drush') !== FALSE) {

        $note = t('Aegir files were detected by git. It is recommended to add the following to your <code>.gitignore</code> file: ');

        $note .= '<pre>
# Aegir files
sites/sites.php
sites/*/drushrc.php
sites/*/local.settings.php
sites/all/drush/drushrc.php
</pre>';

      }
    }

    if (strpos($environment->git_status, 'modified:') !== FALSE || strpos($environment->git_status, 'deleted:') !== FALSE) {
      $icon = 'warning';
      $label = t('Modified Files');
      $item_class = 'danger';
    }

    if (strpos($environment->git_status, 'Changes to be committed:') !== FALSE) {
      $icon = 'check-square-o';
      $label = t('Staged to Commit');
      $item_class = 'success';
    }

    if (strpos($environment->git_status, 'Your branch is behind') !== FALSE) {
      $icon = 'arrow-left';
      $label = t('Behind');
      $item_class = 'info';
    }

    if (strpos($environment->git_status, 'deleted:') !== FALSE || strpos($environment->git_status, 'deleted:') !== FALSE) {
      $icon = 'warning';
      $label = t('Deleted Files');
      $item_class = 'danger';
    }

    if (strpos($environment->git_status, 'have diverged') !== FALSE) {
      $icon = 'exchange fa-rotate-90';
      $label = t('Diverged');
      $item_class = 'danger';
    }

    ?>
    <div class="list-group-item list-group-item-git">
      <label><?php print t('Git') ?></label>

      <!-- Git Status -->
      <div class="btn-group btn-git-status" role="group">
        <button type="button" class="btn btn-<?php print $item_class; ?>" data-toggle="modal" data-target="#environment-git-status-<?php print $environment->name; ?>">
          <i class="fa fa-<?php print $icon; ?>"></i>
          <?php print $label ?>
        </button>
        <button type="button" class="btn btn-text" data-toggle="modal" data-target="#environment-git-status-<?php print $environment->name; ?>" title="<?php print t('Last Commit'); ?>">
          <?php print $environment->git_last ?>
        </button>
        <div class="modal fade" id="environment-git-status-<?php print $environment->name; ?>" tabindex="-1" role="dialog" aria-labelledby="git-status-modal" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="drush-alias-modal">
                  <?php print $environment->name ?> <?php print t('environment'); ?>
                  <small>Git Information</small>
                </h4>
              </div>
              <div class="modal-body">

                <div class="well">
                  <div class="pull-right">
                    <?php if ($project->git_provider == 'github'): ?>
                      <a href="https://github.com/<?php print $project->github_owner ?>/<?php print $project->github_repo ?>/commit/<?php print $environment->git_sha ?>" class="btn btn-link">
                        <i class="fa fa-github"></i>
                        <?php print t('View Commit on GitHub'); ?>
                      </a>
                    <?php endif; ?>
                    <?php if (!empty($environment->git_status) && module_exists('aegir_commit') && user_access('create commit task')): ?>
                    <a href="<?php print url("hosting_confirm/{$environment->site}/site_commit", array('query' => array('token' => $token))); ?>" class="btn btn-primary">
                      <i class="fa fa-code"></i> <?php print t('Commit & Push'); ?>
                    </a>
                    <?php endif; ?>
                  </div>
                  <?php print t('Below is the current git status of the codebase at <code>@path</code>', array('@path' => $environment->repo_root)); ?>
                </div>

                <?php print theme('devshop_ascii', array('output' => $environment->git_commit)); ?>
                <?php print theme('devshop_ascii', array('output' => $environment->git_status)); ?>
                <?php print theme('devshop_ascii', array('output' => $environment->git_diff)); ?>

                <p>
                  <?php print $note; ?>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

    <div class="environment-task-logs <?php if (!$page) print 'list-group-item' ?>">
        <?php if ($page): ?>
            <?php print $environment->task_logs; ?>
       <?php else: ?>
        <!-- Tasks -->
          <label class="sr-only"><?php print t('Last Task') ?></label>

            <div class="btn-group btn-logs pull-right" role="group">
                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                    <i class="fa fa-list-alt"></i>
                </button>
                <div class="dropdown-menu">
                    <?php print $environment->task_logs; ?>
                </div>
            </div>

          <?php print theme('devshop_task', array('task' => $environment->last_task_node)); ?>
        <?php endif; ?>
    </div>
</div>
