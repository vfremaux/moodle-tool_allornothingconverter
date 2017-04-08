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
 * Backup files management tool.
 *
 * @package    tool_backupmanager
 * @copyright  2015 Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/admin/tool/allornothingconverter/lib.php');

// page parameters

admin_externalpage_setup('toolallornothingconverter');

$action = optional_param('what', '', PARAM_TEXT);
if ($action == 'run') {
    $converter = new \tool_allornothingconverter\converter();
    $output = $converter->run();
}


// Header
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('converter', 'tool_allornothingconverter'));

if (!empty($output)) {
    echo '<pre>';
    echo $output;
    echo '</pre>';
}

$toconvert = 0 + $DB->count_records('question', array('qtype' => 'allornothing'));

echo $OUTPUT->box(get_string('toconvert', 'tool_allornothingconverter', $toconvert));

if ($toconvert) {
    echo '<br/><br/>';
    echo $OUTPUT->single_button(new moodle_url('/admin/tool/allornothingconverter/index.php', array('what' => 'run')), get_string('run', 'tool_allornothingconverter'));
}

// Footer.
echo $OUTPUT->footer();

