<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Admin tool "Domain based company membership" - Local library
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Constants for handling the results of the company handling.
define('TOOL_COMPANYDOMAIN_COMPANY_NOTHANDLED', -3);
define('TOOL_COMPANYDOMAIN_COMPANY_MULTIPLE', -2);
define('TOOL_COMPANYDOMAIN_COMPANY_NONE', -1);
define('TOOL_COMPANYDOMAIN_COMPANY_UNCHANGED', 0);

/**
 * Helper function which adds a user to the company which matches his email address domain.
 *
 * This function is inspired by existing code from local_iomad_signup_user_created() in /local/iomad_signup/lib.php.
 *
 * @param object $user The full user object.
 *
 * @return int The result of the handling. It will either return one of the TOOL_COMPANYDOMAIN_* constants or, if the user
 *             was added to a company, the ID of the company.
 */
function tool_companydomain_handle_company_membership($user) {
    global $CFG, $DB;

    require_once($CFG->dirroot . '/local/iomad/lib/company.php');

    // If local_iomad_signup is configured to handle this user's auth method, return.
    if (!empty($CFG->local_iomad_signup_auth) && in_array($user->auth, explode(',', $CFG->local_iomad_signup_auth))) {
        return TOOL_COMPANYDOMAIN_COMPANY_NOTHANDLED;
    }

    // If the user is already in a company, return.
    if ($DB->record_exists('company_users', array('userid' => $user->id))) {
        return TOOL_COMPANYDOMAIN_COMPANY_UNCHANGED;
    }

    // If the event handler was triggered by the block_iomad_company_admin CSV upload form
    // or the user creation wizard, return.
    // This is a rather dirty hack, but can't be implemented otherwise.
    // It is there to avoid adding a user to a company within a CSV upload or manual creation as IOMAD
    // tries to add the user to the company itself in
    // https://github.com/iomad/iomad/blob/IOMAD_39_STABLE/local/iomad/lib/user.php#L174-L178.
    // But as there isn't a check if the user is already a member of the company (which was done 
    // by the event handler directly after
    // https://github.com/iomad/iomad/blob/IOMAD_39_STABLE/local/iomad/lib/user.php#L131,
    // this would result in a DB index violation exception otherwise.
    $backtrace = debug_backtrace();
    foreach($backtrace as $bt) {
        if (strpos($bt['file'], '/blocks/iomad_company_admin/') !== false &&
		strpos($bt['function'], 'create') !== false &&
	        strpos($bt['class'], 'company_user') !== false) {
            return;
	}
    }
    unset($backtrace);

    // Get this user's email domain.
    list($dump, $emaildomain) = explode('@', $user->email);

    // Check if the given domain is configured in any company.
    $domaininfo = $DB->get_records_sql("SELECT * FROM {company_domains} WHERE " .
                  $DB->sql_compare_text('domain') . " = '" . $DB->sql_compare_text($emaildomain) . "'");

    // If there isn't any company domain record for this domain, return.
    if ($domaininfo == false || count($domaininfo) < 1) {
        return TOOL_COMPANYDOMAIN_COMPANY_NONE;

        // Otherwise, if there is more than company with this domain, return.
    } else if (count($domaininfo) > 1) {
        return TOOL_COMPANYDOMAIN_COMPANY_MULTIPLE;
    }

    // Get company.
    $firstdomain = array_pop($domaininfo);
    $company = new company($firstdomain->companyid);

    // Assign the user to the company.
    $company->assign_user_to_company($user->id);

    // Log the event.
    $logevent = \tool_companydomain\event\company_added::create(array(
            'objectid' => $user->id,
            'context' => context_user::instance($user->id),
            'other' => array(
                    'companyid' => $company->id,
            )
    ));
    $logevent->trigger();

    // Return the company ID as a result.
    return $company->id;
}
