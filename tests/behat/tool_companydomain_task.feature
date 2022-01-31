@tool @tool_companydomain @tool_companydomain_task
Feature: Add previously created users automatically to companies.
  In order to handle all users equally regardless when they were created
  As an admin
  I want that users who were created before the company setup are also added to companies based on their email address domain

  Background:
    Given I log in as "admin"
    And I follow "Dashboard" in the user menu
    # I am automatically redirected to the creation of a company, no need to open the create company page manually.
    And I expand all fieldsets
    And I set the field "Long Name" to "Our little company"
    And I set the field "Short Name" to "ourlittlecompany"
    And I set the field "Location (Town/City)" to "Exampletown"
    And I set the field "Select a country" to "Germany"
    # We do not set the field "List of company domains" at this time yet!
    And I click on "Save new company" "button"

  Scenario: Users are created before the email address domain is set in the company, the scheduled task adds them to the company later
    When the following "users" exist:
      | username | firstname | lastname | email                |
      | johndoe  | John      | Doe      | john.doe@example.com |
      | janedoe  | Jane      | Doe      | jane.doe@example.net |
    And I follow "Dashboard" in the user menu
    # As there is only one company, there is no need to select the company which we want to check. This step may be improved.
    And I click on "#UserAdmin-tab" "css_element"
    And I click on "Edit users" "link" in the "#UserAdmin" "css_element"
    Then I should not see "John Doe" in the "#region-main" "css_element"

    And I follow "Dashboard" in the user menu
    # As there is only one company, there is no need to select the company which we want to check. This step may be improved.
    And I click on "#CompanyAdmin-tab" "css_element"
    And I click on "Edit company" "link" in the "#CompanyAdmin" "css_element"
    And I expand all fieldsets
    And I set the field "List of company domains" to "example.com"
    And I click on "Save changes" "button"
    And I run the scheduled task "tool_companydomain\task\update_company_memberships"

    And I follow "Dashboard" in the user menu
    # As there is only one company, there is no need to select the company which we want to check. This step may be improved.
    And I click on "#UserAdmin-tab" "css_element"
    And I click on "Edit users" "link" in the "#UserAdmin" "css_element"
    Then I should see "John Doe" in the "#region-main" "css_element"
    Then I should not see "Jane Doe" in the "#region-main" "css_element"
