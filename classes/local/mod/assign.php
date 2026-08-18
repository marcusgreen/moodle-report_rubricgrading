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
 * Manager for assign module support in the report.
 *
 * @package    report_rubricgrading
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_rubricgrading\local\mod;

use report_rubricgrading\local\plugin_base;
use stdClass;

// Not included when exporting a reportbuilder report.
require_once $CFG->dirroot . '/grade/grading/lib.php';

class assign extends plugin_base {
    #[\Override]
    protected function load_grading_manager(): void {
        $this->gradingmanager = get_grading_manager($this->cm->context, 'mod_assign', 'submissions');
    }

    protected function get_sql_rubric(): string {
        return "SELECT grf.id,
                      ag.userid,
                      ag.grade,
                      asg.grade              AS gradeoutof,
                      afc.commenttext        AS overallfeedback,
                      ag.timemodified        AS timegraded,
                      stu.firstname,
                      stu.lastname,
                      stu.email,
                      stu.username,
                      stu.idnumber,
                      stu.firstnamephonetic,
                      stu.lastnamephonetic,
                      stu.middlename,
                      stu.alternatename,
                      grdr.firstname         AS grader_firstname,
                      grdr.lastname          AS grader_lastname,
                      grdr.firstnamephonetic AS grader_firstnamephonetic,
                      grdr.lastnamephonetic  AS grader_lastnamephonetic,
                      grdr.middlename        AS grader_middlename,
                      grdr.alternatename     AS grader_alternatename,
                      grc.id                 AS criterionid,
                      gl.score,
                      gl.definition          AS leveldef,
                      grf.remark
                 FROM {gradingform_rubric_fillings}  grf
                 JOIN {gradingform_rubric_criteria}  grc  ON grc.id  = grf.criterionid
                 JOIN {gradingform_rubric_levels}    gl   ON gl.id   = grf.levelid
                 JOIN {grading_instances}            gin  ON gin.id  = grf.instanceid AND gin.status = 1
                 JOIN {assign_grades}                ag   ON ag.id   = gin.itemid
                 JOIN {grading_definitions}          gd   ON gd.id   = gin.definitionid
                 JOIN {grading_areas}                ga   ON ga.id   = gd.areaid
                 JOIN {context}                      ctx  ON ctx.id  = ga.contextid
                 JOIN {course_modules}               cm   ON cm.id   = ctx.instanceid AND cm.id  = :cmid
                 JOIN {assign}                       asg  ON asg.id  = cm.instance
            LEFT JOIN {assignfeedback_comments}      afc  ON afc.grade = ag.id
                 JOIN {user}                         stu  ON stu.id  = ag.userid AND stu.deleted = 0
                 JOIN {user}                         grdr ON grdr.id = ag.grader
             ORDER BY stu.lastname, stu.firstname, grc.sortorder";
    }

    #[\Override]
    protected function get_sql_rubric_ranges(): string {
        return "SELECT grf.id,
                      ag.userid,
                      ag.grade,
                      asg.grade              AS gradeoutof,
                      afc.commenttext        AS overallfeedback,
                      ag.timemodified        AS timegraded,
                      stu.firstname,
                      stu.lastname,
                      stu.email,
                      stu.username,
                      stu.idnumber,
                      stu.firstnamephonetic,
                      stu.lastnamephonetic,
                      stu.middlename,
                      stu.alternatename,
                      grdr.firstname         AS grader_firstname,
                      grdr.lastname          AS grader_lastname,
                      grdr.firstnamephonetic AS grader_firstnamephonetic,
                      grdr.lastnamephonetic  AS grader_lastnamephonetic,
                      grdr.middlename        AS grader_middlename,
                      grdr.alternatename     AS grader_alternatename,
                      grc.id                 AS criterionid,
                      grf.grade              AS score,
                      gl.definition          AS leveldef,
                      grf.remark
                 FROM {gradingform_rubric_ranges_f}  grf
                 JOIN {gradingform_rubric_ranges_c}  grc  ON grc.id  = grf.criterionid
                 JOIN {gradingform_rubric_ranges_l}  gl   ON gl.id   = grf.levelid
                 JOIN {grading_instances}            gin  ON gin.id  = grf.instanceid AND gin.status = 1
                 JOIN {assign_grades}                ag   ON ag.id   = gin.itemid
                 JOIN {grading_definitions}          gd   ON gd.id   = gin.definitionid
                 JOIN {grading_areas}                ga   ON ga.id   = gd.areaid
                 JOIN {context}                      ctx  ON ctx.id  = ga.contextid
                 JOIN {course_modules}               cm   ON cm.id   = ctx.instanceid AND cm.id  = :cmid
                 JOIN {assign}                       asg  ON asg.id  = cm.instance
            LEFT JOIN {assignfeedback_comments}      afc  ON afc.grade = ag.id
                 JOIN {user}                         stu  ON stu.id  = ag.userid AND stu.deleted = 0
                 JOIN {user}                         grdr ON grdr.id = ag.grader
             ORDER BY stu.lastname, stu.firstname, grc.sortorder";
    }

    #[\Override]
    protected function get_sql_guide(): string {
        return "SELECT grf.id,
                    ag.userid,
                    ag.grade,
                    asg.grade              AS gradeoutof,
                    afc.commenttext        AS overallfeedback,
                    ag.timemodified        AS timegraded,
                    stu.firstname,
                    stu.lastname,
                    stu.email,
                    stu.username,
                    stu.idnumber,
                    stu.firstnamephonetic,
                    stu.lastnamephonetic,
                    stu.middlename,
                    stu.alternatename,
                    grdr.firstname         AS grader_firstname,
                    grdr.lastname          AS grader_lastname,
                    grdr.firstnamephonetic AS grader_firstnamephonetic,
                    grdr.lastnamephonetic  AS grader_lastnamephonetic,
                    grdr.middlename        AS grader_middlename,
                    grdr.alternatename     AS grader_alternatename,
                    grc.id                 AS criterionid,
                    grf.score,
                    grf.remark
                FROM {gradingform_guide_fillings}   grf
                JOIN {gradingform_guide_criteria}   grc  ON grc.id  = grf.criterionid 
                JOIN {grading_instances}            gin  ON gin.id  = grf.instanceid AND gin.status = 1
                JOIN {assign_grades}                ag   ON ag.id   = gin.itemid
                JOIN {grading_definitions}          gd   ON gd.id   = gin.definitionid
                JOIN {grading_areas}                ga   ON ga.id   = gd.areaid
                JOIN {context}                      ctx  ON ctx.id  = ga.contextid
                JOIN {course_modules}               cm   ON cm.id   = ctx.instanceid AND cm.id  = :cmid
                JOIN {assign}                       asg  ON asg.id  = cm.instance
                LEFT JOIN {assignfeedback_comments}      afc  ON afc.grade = ag.id
                JOIN {user}                         stu  ON stu.id  = ag.userid AND stu.deleted = 0
                JOIN {user}                         grdr ON grdr.id = ag.grader
                ORDER BY stu.lastname, stu.firstname, grc.sortorder";
    }

    #[\Override]
    public function get_row_key(stdClass $row): mixed {
        return $row->userid;
    }

}