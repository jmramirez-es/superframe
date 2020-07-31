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
 * superframe rendered page
 *
 * @package    block_superframe
 * @copyright  Daniel Neis <danielneis@gmail.com>
 * Modified for use in MoodleBites for Developers Level 1 by Richard Jones & Justin Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_superframe_renderer extends plugin_renderer_base 
{

    function display_view_page($url, $width, $height, $courseid)
    {
        global $USER;

        $data = new stdClass();

        // Page heading and iframe data.
        $data->heading = get_string('pluginname', 'block_superframe');
        $data->url = $url;
        $data->height = $height;
        $data->width = $width;
        $data->courseid = $courseid;
        
        $data->username = fullname($USER);

        //return link 
        $data->returnlink = new moodle_url('/course/view.php',
                ['id' => $courseid]);
        $data->returntext = get_string('returncourse', 'block_superframe');

        // Start output to browser.
        echo $this->output->header();

        // Render the data in a Mustache template.
        echo $this->render_from_template('block_superframe/frame', $data);

        // Finish the page.
        echo $this->output->footer();
    }
   

   function fetch_block_content($blockid, $students, $courseid) 
   {
       //variable global User.
        global $USER, $DB;
        
        $data = new stdClass();

        // creamos un array llamado studentlist dentro del objeto data
        $data->studentlist = array();
        $data->studentPicturelist = array();
        
        //con este foreach recorremos el listado de estudiantes que recibimos por parametro. y guardamos el dato firstname en el array definido antes
        foreach ($students as $student) {
             $studentlist['name'] = $student->firstname; 
             $rs = $DB->get_record_select("user", "id = '$student->id'", null, user_picture::fields());
             //var_dump($rs);exit;
             $studentlist['pic'] = $this->output->user_picture($rs);
             $data->students[] = $studentlist;
        }

        //asignamos el nombre completo a la variable username
        $username = fullname($USER);
        //se carga desde los strings de multilenguaje el mensaje de bienvenida
        $data->welcome = get_string('welcomeuser', 'block_superframe', $USER);
        //se guarda la url que carga el blocks mediante moodle_url y se almacena en la variable data->url
        $data->url = new moodle_url('/blocks/superframe/view.php', ['blockid' => $blockid, 'courseid' => $courseid]);
        // se carga desde los strings de multilenguaje el mensaje texto del link
        $data->linktext = get_string('viewlink', 'block_superframe');
        // asigna el array de estudiantes a la variable students
        //$data->students = $studentlist;

        return $this->render_from_template('block_superframe/block', $data);
   }

}