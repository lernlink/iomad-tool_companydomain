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
 * Admin tool "Domain based company membership" - Scheduled task
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_companydomain\task;

/**
 * The tool_companydomain drop update company memberships task class.
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_company_memberships extends \core\task\scheduled_task {
    /**
     * Return localised task name.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskupdatecompanymemberships', 'tool_companydomain');
    }

    /**
     * Execute scheduled task
     *
     * @return boolean
     */
    public function execute() {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/admin/tool/companydomain/locallib.php');

        // Tracing.
        mtrace('Handling the company membership of all (non-deleted and non-suspended) users...');

        // Load all existing users into a record set.
        $sqlparams = array('deleted' => 0, 'suspended' => 0);
        $recordset = $DB->get_recordset_select('user', "deleted = :deleted AND suspended = :suspended", $sqlparams);

        // Initialize variable to count the number of users.
        $usercount = 0;

        // Iterate over all users.
        foreach ($recordset as $user) {
            // Silently skip the guest user.
            if ($user->id == $CFG->siteguest) {
                continue;
            }

            // Increment the user counter.
            $usercount++;

            // Handle the company memberships.
            $result = tool_companydomain_handle_company_membership($user);

            // Tracing.
            $userstring = '... '.fullname($user).' (User ID '.$user->id.'):'.PHP_EOL;
            if ($result == TOOL_COMPANYDOMAIN_COMPANY_NOTHANDLED) {
                mtrace ($userstring.'    INFO: The user\'s auth method is configured to be handled by local_iomad_signup. '.
                        'The user was not added to any company.');
            } else if ($result == TOOL_COMPANYDOMAIN_COMPANY_MULTIPLE) {
                mtrace ($userstring.'    WARNING: Multiple companies with the given email domain found. '.
                        'The user was not added to any company.');
            } else if ($result == TOOL_COMPANYDOMAIN_COMPANY_NONE) {
                mtrace ($userstring.'    INFO: There isn\'t any company with the given email domain. '.
                        'The user was not added to any company.');
            } else if ($result == TOOL_COMPANYDOMAIN_COMPANY_UNCHANGED) {
                mtrace ($userstring.'    SUCCESS: The user is already a member of a company. '.
                        'The user\'s company was left unchanged.');
            } else if ($result > TOOL_COMPANYDOMAIN_COMPANY_UNCHANGED) {
                mtrace ($userstring.'    SUCCESS: The user isn\'t a member of a company yet. '.
                        'The user was added to company '.$result).'.';
            }
        }

        // Tracing.
        if ($usercount == 0) {
            mtrace ('... There aren\'t any users to handle.');
        } else {
            mtrace ('... DONE. '.$usercount.' users were handled.');
        }

        // Close record set.
        $recordset->close();

        return true;
    }
}
