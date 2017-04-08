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
 * @package    tool_allornothingconverter
 * @category   tool
 * @copyright  2016 Valery Fremaux <valery@edunao.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_allornothingconverter;

class converter {

    function run() {
        global $DB;


        $questions = $DB->get_records('question', array('qtype' => 'allornothing'));

        $output = '';

        if (!empty($questions)) {
            foreach ($questions as $q) {
                $output.= $this->convert_question($q);
            }
        }

        return $output;
    }

    function convert_question($question) {
        global $DB;

        $output = "Converting question ID $question->id\n";
        $allornothing = $DB->get_record('qtype_allornothing', array('question' => $question->id));
        if (!$allornothing) {
            $output = "Error: Missing allornothing record.\n";
            return $output;
        }
        $answers = $DB->get_records('question_answers', array('question' => $question->id));

        $multichoiceset = new \StdClass();
        $multichoiceset->questionid = $question->id;
        $multichoiceset->layout = 0 + @$allornothing->layout;
        $multichoiceset->shuffleanswers = $allornothing->shuffleanswers;
        $multichoiceset->correctfeedback = $allornothing->correctfeedback;
        $multichoiceset->correctfeedbackformat = $allornothing->correctfeedbackformat;
        $multichoiceset->incorrectfeedback = $allornothing->incorrectfeedback;
        $multichoiceset->incorrectfeedbackformat = $allornothing->incorrectfeedbackformat;
        $multichoiceset->answernumbering = $allornothing->answernumbering;
        $multichoiceset->shownumcorrect = $allornothing->shownumcorrect;
        $DB->insert_record('qtype_multichoiceset_options', $multichoiceset);

        $question->qtype = 'multichoiceset';
        $DB->update_record('question', $question);
        $output .= "converted\n\n";

        if (!empty($answers)) {
            $output .= "Fixing answer record fractions...\n";
            foreach ($answers as $a) {
                if ($a->fraction > 0) {
                    $a->fraction = 1.00;
                    $DB->update_record('question_answers', $a);
                    $output .= "Fixing answer record fraction $a->id\n";
                }
            }
        }

        $DB->delete_records('qtype_allornothing', array('id' => $allornothing->id));
        $output .= "done.\n\n";
        return $output;
    }

}