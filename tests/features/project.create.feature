@api
Feature: Create a project
  In order to start developing a drupal site
  As a project admin
  I need to create a new project

  Scenario: Create a new drupal 8 project

    Given I am logged in as a user with the "administrator" role
    And I am on the homepage
    When I click "Projects"
    And I click "Start a new Project"
    Then I should see "Step 1"
    Then I fill in "drpl8" for "Project Code Name"
    And I fill in "http://github.com/opendevshop/drupal_docroot.git" for "Git URL"
    When I press "Next"

    # Step 2
    Then I should see "drpl8"
    And I should see "http://github.com/opendevshop/drupal_docroot.git"
    When I fill in "docroot" for "Path to Drupal"

    # Step 3
    When I press "Next"
    Then I should see "Please wait while we connect to your repository and determine any branches."
#    And I should see "Path to Drupal: docroot"

    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output
    And I reload the page
    And I reload the page

    Then I should see "Create as many new environments as you would like."
    When I fill in "dev" for "project[environments][NEW][name]"
    And I select "master" from "project[environments][NEW][git_ref]"

    And I press "Add environment"
    And I fill in "live" for "project[environments][NEW][name]"
    And I select "8.0" from "project[environments][NEW][git_ref]"
    And I press "Add environment"
    Then I press "Next"

    # Step 4
    And I should see "dev"
    And I should see "live"
    And I should see "master"
    And I should see "8.0"

    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output
    And I reload the page

    Then I should see "dev"
    And I should see "live"
    And I should see "master"

    And I should see "master"
    And I reload the page
#    When I click "Process Failed"
    Then I should see "8."
    Then I should not see "Platform verification failed"
    When I select "standard" from "install_profile"

#    Then I break

    And I press "Create Project & Environments"

    # FINISH!
    Then I should see "Your project has been created. Your sites are being installed."
    And I should see "Dashboard"
    And I should see "Settings"
    And I should see "Logs"
    And I should see "standard"
#    And I should see "http://github.com/opendevshop/drupal"
    And I should see the link "dev"
    And I should see the link "live"

#    Then I break
    And I should see the link "http://drpl8.dev.devshop.local.computer"
    And I should see the link "Aegir Site"

    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output
    Then drush output should not contain "This task is already running, use --force"

    And I reload the page
    Then I should see the link "dev"
    Then I should see the link "live"
#    Given I go to "http://dev.drpl8.devshop.travis"
#    When I click "Visit Environment"

# @TODO: Fix our site installation.
#    Then I should see "No front page content has been created yet."

    When I click "Create New Environment"
    And I fill in "testenv" for "Environment Name"
    And I select "master" from "Branch or Tag"
    And I select the radio button "Drupal Profile"
    Then I select the radio button "Standard Install with commonly used features pre-configured."

    #@TODO: Check lots of settings

    Then I press "Create New Environment"
    Then I should see "Environment testenv created in project drpl8."

    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output
    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output
    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output

    When I click "testenv" in the "main" region
    Then I should see "Environment Dashboard"
    And I should see "Environment Settings"

    When I click "Visit Site"
    Then I should see "Welcome to drpl8.testenv"

    Then I move backward one page
    When I click "Project Settings"
    Then I select "testenv" from "Primary Environment"
    And I press "Save"

    Then I should see "DevShop Project drpl8 has been updated."
    And I should see an ".environment-link .fa-bolt" element

    When I click "Visit Webhook"
    When I should get a "403" HTTP response
    Then I should see "is not authorized to invoke a Pull Code request"

    Then I am at "admin/hosting/git"
    And I fill in " " for "Control Access by IP"
    Then I press "Save configuration"
    Then print last response

    Then I am on the homepage
    And I click "drpl8"
    When I run drush "vget" "hosting_git_pull_webhook_ip_acl"
    Then print last drush output

    When I click "Visit Webhook"
    Then print last response
    Then I should see "Environments found: testenv, dev, live"
#    And I should see "Task started"

    Given I move backward one page
    When I reload the page

    Then I should see "Git pull" in the "#drpl8-testenv .last-task-alert .alert-queued" element
    And I should see "Git pull" in the "#drpl8-dev .last-task-alert .alert-queued" element

    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output

    When I reload the page
# Tasks on CentOS were failing with a warning. Using these steps I found the issue to be missing date timezone setting.
#    When I click "Git pull Warning"
#    Then print last response

    Then I should see "Git pull" in the "#drpl8-testenv .last-task-alert .alert-success" element
    And I should see "Git pull" in the "#drpl8-dev .last-task-alert .alert-success" element

    # Test Checkout
    Given I click "live" in the "main" region
    When I click "8.3.5" in the "main" region
    Then I should see "Git Hooks"
    And I should see "Update: Run database updates"
    When I press "Git checkout"
    Then I should see "Git checkout" in the "#drpl8-live .last-task-alert .alert-queued" element

    # Run twice so platform and site verify tasks run
    When I run drush "hosting-tasks --force --fork=0 --strict=0"
    Then print last drush output

    When I reload the page
    Then I should see "Git checkout" in the "#drpl8-live .last-task-alert .alert-success" element
