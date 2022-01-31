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
 * Admin tool "Domain based company membership" - Event definition
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_companydomain\event;

/**
 * The tool_companydomain company added event class.
 *
 * @package    tool_companydomain
 * @copyright  2022 Alexander Bias, lern.link GmbH <alexander.bias@lernlink.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class company_added extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'user';
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventcompanyadded', 'tool_companydomain');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return get_string('eventcompanyadded_desc', 'tool_companydomain', array('userid' => $this->objectid,
                'companyid' => $this->other['companyid']));
    }

    /**
     * Get url related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/user/profile.php', array('id' => $this->objectid));
    }
}
