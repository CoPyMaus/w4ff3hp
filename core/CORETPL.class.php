<?php
if (!class_exists('CORETPL')) {
    class CORETPL
    {

        public function __construct()
        {
            $this->template = false;
            $this->filecontent = false;
            global $core;
            $this->core =& $core;
        }

        public function load_template()
        {
            if (!defined('TEMPLATE_PATH')) die ('It is not set the TEMPLATE_PATH in Settings');
            if (file_exists(TEMPLATE_PATH . TEMPLATE . 'index.tpl')) {
                $this->template = file_get_contents(TEMPLATE_PATH . TEMPLATE . 'index.tpl');
                $this->replace_keywords();
            } else
                die('Couldnt find ' . TEMPLATE_PATH . TEMPLATE . 'index.tpl');
        }

        private function replace_keywords()
        {
//global $core;
            if (isset($matches)) {
                unset($matches);
            }
            preg_match_all('/{(.*?)}/i', $this->template, $matches);
            if ($matches) {
                $this->core->set_debug_message("PREG_MATCH_ALL -> Matches found in class CORETPL on function replace_keywords");
                foreach ($matches[1] as $key) {
                    if (strpos($key, ":") == true) {
                        $this->core->set_debug_message("The delemiter ':' was found in class CORETPL on function replace_keywords");
                        list($func, $keyword) = explode(":", $key);
                        $this->core->set_debug_message("Function " . $func . " with keyword '" . $keyword . "'' founded in class CORE_TPL on function replace_keywords");
                        switch ($func) {
                            case 'tpl':
                                $this->insert_into_template($keyword, $key); //Description in the function
                                break;
                            case 'lang':
                                $this->replace_key2lang($keyword, $key); //Description in the function
                                break;
                            case 'php_include':
                                $this->php_include($keyword, $key);
                                break;
                            default:
                        }
                    } else {
                        if (defined($key)) $this->template = str_replace('{' . $key . '}', constant($key), $this->template);
                    }
                }
            }
        }

        private function php_include($keyword, $key)
        {
            require_once(TEMPLATE_PATH . TEMPLATE . 'modules/' . $keyword . '/' . $keyword . '.php');

            if (isset($this->content_replace) && !empty($this->content_replace)) $this->template = str_replace('{' . $key . '}', $this->content_replace, $this->template);
        }

        public function simple_replace($content, $array)
        {
            if (!empty($content) && is_array($array)) {
                foreach ($array as $key => $value) {
                    $content = str_replace($key, $value, $content);
                }
            }

            return ($content);
        }

        public function getcontent_box($template, $return = false)
        {
            if (file_exists($template.'.tpl')) $this->filecontent = file_get_contents($template.'.tpl'); else
                die('Error in template: Cannot find ' . $template.'.tpl');

            if ($return) return ($this->filecontent);
        }

        public function getcontent($template, $mode = 'snippet', $return = false)
        {
            $template = ($mode == 'snippet') ? $template . '_snippet.tpl' : $template . '.tpl';
            if (file_exists(TEMPLATE_PATH . TEMPLATE . $template)) $this->filecontent = file_get_contents(TEMPLATE_PATH . TEMPLATE . $template); else
                die('Error in template: Cannot find ' . TEMPLATE_PATH . TEMPLATE . $template);

            if ($return) return ($this->filecontent);
        }

        private function insert_into_template($keyword, $key)
        {
//Another template is added to the existing template.
// {tpl:index2} will insert the index2.tpl at the location of the keyword!
            if (file_exists(TEMPLATE_PATH . TEMPLATE . $keyword . '.tpl')) {
                $file = file_get_contents(TEMPLATE_PATH . TEMPLATE . $keyword . '.tpl');
                $this->template = str_replace('{' . $key . '}', $file, $this->template);
                $this->replace_keywords();
            }
        }

        private function replace_key2lang($keyword, $key)
        {
//The defined variable of the language file is inserted instead of the keyword.
//If no variable has been defined, the text of the passed parameter is supplemented:
// {lang:This is a text} adds "This is a text" to the keyword.
// {lang:VERSION} sets the version number from a language file via define("VERSION", "0.0.0001").
            if (defined($keyword)) $this->template = str_replace('{' . $key . '}', constant($keyword), $this->template); else
                $this->template = str_replace('{' . $key . '}', $keyword, $this->template);
            $this->replace_keywords();
        }

        public function template_array($type, $value) //file or string (string can contents HTML or any)
        {
            if ($type == 'file') {
                if (file_exists($value)) {
                    $this->template .= file_get_contents($value);
                }
            }

            if ($type == 'string') $this->template .= $value;
        }
    }
}
?>