<?php
namespace samjoyce\slugger;
/*
 * Class to create and check unique slug in a PDO 
 * by Sam Joyce
 * www.samjoyce.co.uk
 */

class Slugger {

    //database table to search
    protected $table;
    //the field to search to check uniquness
    protected $field;

    public function __construct(\PDO $db, $table = 'users', $field = 'slug') {
        $this->db = $db;
        $this->set($table, $field);
    }

    //allow for reset and chain
    public function set($table, $field) {
        $this->table = $table;
        $this->field = $field;
        return $this;
    }
    
    
     /* MAIN ENTRY TO THIS CLASS
     * return slug if unique else search for unique
     * @string string to turn into slug and check
     * @option array of additional to make slug suggestion
     */
    public function create($string, $options) {
        $slug = $this->makeString($string);
        $unique = $this->check($slug);

        return $unique ? $slug : $this->extended($slug, $options);
    }

    //make slug string
    // string is the slug
    public function makeString($string) {
        $string = strtolower($string);

        $keep_expression = '/[^0-9a-z\-\s]/';
        $cleaned = preg_replace($keep_expression, '', $string);

        $replace_expression = '/[\-\s]+/';
        $formatted = preg_replace($replace_expression, "-", $cleaned);

        return trim($formatted, '-');
    }

    //check if slug is unique in PDO database
    public function check($slug) {
        $handler = $this->db->prepare("SELECT $this->field FROM $this->table WHERE $this->field = $slug");
        $handler->execute();
        $search = $handler->fetchAll();
        return count($search) ? false : true;
    }

    //rotate through to find unique slug
    public function extended($slug, $options) {
        foreach ($options as $option) {
            $slug_extended = $this->makeString($slug . '-' . $option);
            $unique = $this->check($slug_extended);
            if ($unique)
                return $slug_extended;
        }

        //last resort slug creation
        return $this->random($slug);
    }

    //create a unique slug with a number
    public function random($slug){
        $unique = false;
         while (!$unique) {
            $slug_extended = $this->makeString($slug . '-' . rand(10, 1000));
            $unique = $this->check($slug_extended);
        }
        return $slug_extended;
    }
}
