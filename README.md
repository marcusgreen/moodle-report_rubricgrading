# moodle-report_rubricgrading [![Moodle Plugin CI](https://github.com/marcusgreen/moodle-report_rubricgrading/actions/workflows/moodle-ci.yml/badge.svg)](https://github.com/marcusgreen/moodle-report_rubricgrading/actions/workflows/moodle-ci.yml)

Created by Marcus Green. Report to display a breakdown of rubric grading for assignments that use the Rubric advanced grading method. Each student appears as a single row, with the rubric criteria spread across columns showing the score, level definition and grader feedback for each criterion alongside the overall grade and feedback. Supports export/download to Excel or CSV.

Contact Moodle Partner Catalyst EU (https://www.catalyst-eu.net/) for custom development and consultancy.

Install from the command line at the root of your Moodle installation as follows.

```
git clone https://github.com/marcusgreen/moodle-report_rubricgrading report/rubricgrading
```


## Requirements

- Moodle 4.5 or later
- An assignment (or supported activity type) configured to use the **Rubric, Ranged Rubric, or Marking Guide** advanced grading method.

## Usage

(Assumption: Using assignment module).

Open an assignment that uses rubric grading. A **Rubric grading report** link will appear in the assignment navigation menu. Clicking it opens the report for that assignment.

The report displays one row per student with the following columns:

- **Student** — full name
- One group of columns per rubric criterion, each containing:
  - Score awarded
  - Level definition selected by the grader
  - Per-criterion feedback
- **Overall feedback** — the grader's overall comment
- **Grade** — the final numeric grade
- **Graded by** — the teacher who submitted the grade
- **Time graded** — when the grade was recorded

Filters are available for student name, email, ID number, grade, date graded and per-criterion scores. The report can be downloaded as an Excel spreadsheet or CSV file using the download button.

