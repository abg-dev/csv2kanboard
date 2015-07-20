CSV2Kanboard
==============

A quick and dirty PHP script to import CSV files in Kanboard (http://kanboard.net/). Till we have native CSV import in Kanboard itself.

## What is does
- Creates tasks in Kanboard from CSV file. Useful for bulk task creation.
- Create tasks in multiple projects, swimlanes, with different color codes... whatever Kanboard Webhook API allows
- Supports fields while creation: project_id, title, description, color_id, owner_id, column_id

## Prerequisites

- PHP 5.x. Tested on PHP 5.5+
- Tested against Release **1.0.12**, **latest Kanboard development** branch.

## Usage

1. Download and put the csv2kanboard.php file somewhere.
2. **IMPORTANT**: You must set the Kanban webhook URL. Get it from http://<kanboard_url>/?controller=config&action=webhook. You can set it to one of these files:
    - A file with name `.csv2kanboard` in the same directory. Just paste it in the file and make sure it's the first line.
    - Open the `csv2kanboard.php` and set it there at line number 7. This is overridden if mentioned in `.csv2kanboard` file.
3. Run `php csv2kanboard.php <filename>`. You need to pass a comma delimited filename as argument.

**Example command line:**

`php csv2kanboard.php sample.csv`

*Note: A sample data file is included. You can test with that -or- start creating your task list from it.*

The CSV should be structured this way:

- First line is assumed as header and skipped.
- Columns must be in this order: project_id, title, description, color_id, owner_id, column_id
- **Mandatory fields:**
- project_id: If not provided, row will be skipped
- title: If not provided, row will be skipped
- *[tips: These can be used to ignore a line. For example you want to skip certain lines (used as section identifier or header), set either of them blank.]*
- **Optional field values:**
- description: Can contain markdown syntax
- color_id: yellow, blue, green, purple, red, orange and grey. Default yellow. **Anything else will create task with white background. But later you can always change it**
- owner_id: Numeric owner id. Default unassigned
- column_id: Numeric column id. Defaults to first column on board. If column id does not belong to this project, **task is not created and no error is shown**
- category_id: Numeric category id
- Blank lines are ignored. 

 ## Troubleshooting tips
 
* If it says 'Not Authorized', it means the token is not valid.
* The webhook URL can be checked from browser. Call this URL from browser it's alright if you get a FAILED message. If you see 'Not Authorized' then this is not correct.
 
