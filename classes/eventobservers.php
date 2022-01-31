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
 * Admin tool "Domain based company membership" - Event observers
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_companydomain;

/**
 * Observer class containing methods monitoring various events.
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class eventobservers {

    /**
     * User created event observer.
     *
     * @param \core\event\base $event The event.
     */
    public static function user_created(\core\event\base $event) {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/admin/tool/companydomain/locallib.php');

        // If we do not have a related user ID, return.
        if (!isset($event->relateduserid) || !is_int((int)$event->relateduserid)) {
            return;
        }

        // Get the full user object with the given related user id.
        $user = \core_user::get_user($event->relateduserid);

        // Handle the company membership.
        tool_companydomain_handle_company_membership($user);
    }
}
