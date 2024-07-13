<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * CodeIgniter.
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @author  ExpressionEngine Dev Team
 * @copyright  Copyright (c) 2006, EllisLab, Inc.
 * @license http://codeigniter.com/user_guide/license.html
 *
 * @see http://codeigniter.com
 * @since   Version 1.0
 *
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * CodeIgniter Template Class.
 *
 * This class is and interface to CI's View class. It aims to improve the
 * interaction between controllers and views. Follow @see for more info
 *
 * @author      Colin Williams
 *
 * @category    Libraries
 *
 * @see        http://www.williamsconcepts.com/ci/libraries/template/index.html
 *
 * @copyright  Copyright (c) 2008, Colin Williams.
 *
 * @version 1.4.1
 */
class Template
{
    public $CI;
    public $config;
    public $template;
    public $master;
    public $folder;
    public $asset_fld = 'asset'; // path to the folder which contains templates' CSS, javascript... files, relative to Front Controller
    public $default_arguments = [];
    public $output;
    public $regions = [];
    public $default_regions = [
        '_scripts' => [],
        '_styles' => [],
    ];
    public $js = [];
    public $css = [];
    public $parser = 'parser';
    public $parser_method = 'parse';
    public $parse_template = false;
    public $protocol_pattern = '{^http://|^https://|^ftp://}';

    /**
     * Constructor.
     *
     * Loads template configuration, template regions, and validates existence of
     * default template
     */
    public function __construct()
    {
        // Copy an instance of CI so we can use the entire framework.
        $this->CI = &get_instance();
        $this->CI->load->library('user_agent');

        // Load the template config file and setup our master template and regions
        include APPPATH.'config/template.php';
        if (isset($template)) {
            $this->config = $template;
            $this->set_template($template['active_template']);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Use given template settings.
     *
     * @param   string   array key to access template settings
     * @param mixed $group
     */
    public function set_template($group)
    {
        if (isset($this->config[$group])) {
            $this->folder = $group.'/'; // By default, the Template Folder is the Template group name
            $this->template = $this->config[$group];
        } else {
            show_error('The "'.$group.'" template group does not exist. Provide a valid group name or add the group first.');
        }
        $this->initialize($this->template);
    }

    // --------------------------------------------------------------------

    /**
     * Set master template.
     *
     * @param   string   filename of new master template file
     * @param mixed $filename
     */
    public function set_master_template($filename)
    {
        $filepath = $this->folder.$filename;
        if (file_exists(APPPATH.'views/'.$filepath) or file_exists(APPPATH.'views/'.$filepath.'.php')) {
            $this->master = $filepath;
        } else {
            show_error('The filename provided does not exist in <strong>'.APPPATH.'views</strong>. Remember to include the extension if other than ".php"');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Dynamically add a template and optionally switch to it.
     *
     * @param   string   array key to access template settings
     * @param   array properly formed
     * @param mixed $group
     * @param mixed $template
     * @param mixed $activate
     */
    public function add_template($group, $template, $activate = false)
    {
        if (!isset($this->config[$group])) {
            $this->config[$group] = $template;
            if (true === $activate) {
                $this->initialize($template);
            }
        } else {
            show_error('The "'.$group.'" template group already exists. Use a different group name.');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Initialize class settings using config settings.
     *
     * @param   array   configuration array
     * @param mixed $props
     */
    public function initialize($props)
    {
        // Set Template Folder
        if (isset($props['folder'])) {
            if ('' !== trim($props['folder'])) {
                $this->folder = $props['folder'].'/';
            } else {
                $this->folder = '';
            }
        }

        // Set master template
        if (isset($props['template'])) {
            $this->set_master_template($props['template']);
        } else {
            // Master template must exist. Throw error.
            show_error('Either you have not provided a master template or the one provided does not exist in <strong>'.APPPATH.'views</strong>. Remember to include the extension if other than ".php"');
        }

        // Load default arguments
        if (isset($props['default_args'])) {
            $this->set_default_args($props['default_args']);
        }

        // Load our regions
        if (isset($props['regions'])) {
            $this->set_regions($props['regions']);
        }

        // Set parser and parser method
        if (isset($props['parser'])) {
            $this->set_parser($props['parser']);
        }
        if (isset($props['parser_method'])) {
            $this->set_parser_method($props['parser_method']);
        }

        // Set master template parser instructions
        $this->parse_template = isset($props['parse_template']) ? $props['parse_template'] : false;
    }

    // --------------------------------------------------------------------

    /**
     * Set regions for writing to.
     *
     * @param   array   properly formed regions array
     * @param mixed $regions
     */
    public function set_regions($regions)
    {
        if (count($regions)) {
            // Reset existed $regions to default
            $this->regions = $this->default_regions;

            foreach ($regions as $key => $region) {
                // Regions must be arrays, but we take the burden off the template
                // developer and insure it here
                if (!is_array($region)) {
                    $this->add_region($region);
                } else {
                    $this->add_region($key, $region);
                }
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set default arguments that be used cross views.
     *
     * @param   array   properly formed arguments array
     * @param mixed $default_args
     */
    public function set_default_args($default_args)
    {
        if (is_array($default_args)) {
            $this->CI->load->vars($default_args);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Dynamically add region to the currently set template.
     *
     * @param   string   Name to identify the region
     * @param   array Optional array with region defaults
     * @param mixed $name
     * @param mixed $props
     */
    public function add_region($name, $props = [])
    {
        if (!is_array($props)) {
            $props = [];
        }

        if (!isset($this->regions[$name])) {
            $this->regions[$name] = $props;
        } else {
            show_error('The "'.$name.'" region has already been defined.');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Empty a region's content.
     *
     * @param   string   Name to identify the region
     * @param mixed $name
     */
    public function empty_region($name)
    {
        if (isset($this->regions[$name]['content'])) {
            $this->regions[$name]['content'] = [];
        } else {
            show_error('The "'.$name.'" region is undefined.');
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set parser.
     *
     * @param mixed      $parser
     * @param null|mixed $method
     */
    public function set_parser($parser, $method = null)
    {
        $this->parser = $parser;
        $this->CI->load->library($parser);

        if ($method) {
            $this->set_parser_method($method);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Set parser method.
     *
     * @param   string   name of parser class member function to call when parsing
     * @param mixed $method
     */
    public function set_parser_method($method)
    {
        $this->parser_method = $method;
    }

    // --------------------------------------------------------------------

    /**
     * Write contents to a region.
     *
     * @param string $region    region to write to
     * @param string $content   what to write
     * @param bool   $overwrite FALSE to append to region, TRUE to overwrite region
     */
    public function write($region, $content, $overwrite = false)
    {
        if (isset($this->regions[$region])) {
            if (true === $overwrite) { // Should we append the content or overwrite it
                $this->regions[$region]['content'] = [$content];
            } else {
                $this->regions[$region]['content'][] = $content;
            }
        }

        // Regions MUST be defined
        else {
            show_error("Cannot write to the '{$region}' region. The region is undefined.");
        }
    }

    // --------------------------------------------------------------------

    /**
     * Write content from a View to a region. 'Views within views'.
     *
     * @param string     $region    region to write to
     * @param string     $view      view file to use
     * @param null|array $data      variables to pass into view
     * @param bool       $overwrite FALSE to append to region, TRUE to overwrite region
     */
    public function write_view($region, $view, $data = null, $overwrite = false)
    {
        $view = $this->folder.$view;
        $args = func_get_args();

        // Get rid of non-views
        unset($args[0], $args[2], $args[3]);

        // Do we have more view suggestions?
        if (count($args) > 1) {
            foreach ($args as $suggestion) {
                $suggestion = $this->folder.$suggestion;
                $filepaths = [
                    APPPATH.'views/'.$suggestion.'.php',
                    APPPATH.'views/'.$suggestion,
                ];
                foreach ($filepaths as $filepath) {
                    if (file_exists($filepath)) {
                        // Just change the $view arg so the rest of our method works as normal
                        $view = $filepath;

                        break 2;
                    }
                }
            }
        }

        $content = $this->CI->load->view($view, $data, true);
        $this->write($region, $content, $overwrite);
    }

    // --------------------------------------------------------------------

    /**
     * Load content of a View without writing it to the region of master template
     * This method is basicly the same with CI->load->view, but it will add the
     * template folder at the begining of viewpath to classify which template is used.
     *
     * @param string     $view          view file to use
     * @param null|array $data          variables to pass into view
     * @param bool       $get_by_string type of method for handle output data
     *
     * @return string if method is TRUE, this will return data view into string
     *                if method is FALSE, this will write out data to browser
     */
    public function get_view($view, $data = null, $get_by_string = true)
    {
        $view = $this->folder.$view;
        $args = func_get_args();

        // Get rid of non-views
        unset($args[0], $args[2], $args[3]);

        // Do we have more view suggestions?
        if (count($args) > 1) {
            foreach ($args as $suggestion) {
                $suggestion = $this->folder.$suggestion;
                $filepaths = [
                    APPPATH.'views/'.$suggestion.'.php',
                    APPPATH.'views/'.$suggestion,
                ];
                foreach ($filepaths as $filepath) {
                    if (file_exists($filepath)) {
                        // Just change the $view arg so the rest of our method works as normal
                        $view = $filepath;

                        break 2;
                    }
                }
            }
        }

        return $this->CI->load->view($view, $data, $get_by_string);
    }

    // --------------------------------------------------------------------

    /**
     * Parse content from a View to a region with the Parser Class.
     *
     * @param   string   region to write to
     * @param   string   view file to parse
     * @param   array variables to pass into view for parsing
     * @param   bool  FALSE to append to region, TRUE to overwrite region
     * @param null|mixed $data
     * @param mixed      $region
     * @param mixed      $view
     * @param mixed      $overwrite
     */
    public function parse_view($region, $view, $data = null, $overwrite = false)
    {
        $this->CI->load->library('parser');

        $view = $this->folder.$view;
        $args = func_get_args();

        // Get rid of non-views
        unset($args[0], $args[2], $args[3]);

        // Do we have more view suggestions?
        if (count($args) > 1) {
            foreach ($args as $suggestion) {
                $suggestion = $this->folder.$suggestion;
                if (file_exists(APPPATH.'views/'.$suggestion.'.php') or file_exists(APPPATH.'views/'.$suggestion)) {
                    // Just change the $view arg so the rest of our method works as normal
                    $view = $suggestion;

                    break;
                }
            }
        }

        $content = $this->CI->{$this->parser}->{$this->parser_method}($view, $data, true);
        $this->write($region, $content, $overwrite);
    }

    // --------------------------------------------------------------------

    /**
     * Dynamically include javascript in the template.
     *
     * NOTE: This function does NOT check for existence of .js file
     *
     * @param   string   script to import or embed
     * @param   string  'import' to load external file or 'embed' to add as-is
     * @param   bool  TRUE to use 'defer' attribute, FALSE to exclude it
     * @param mixed $script
     * @param mixed $type
     * @param mixed $defer
     *
     * @return true on success, FALSE otherwise
     */
    public function add_js($script, $type = 'import', $defer = false)
    {
        $success = true;
        $js = null;

        $this->CI->load->helper('url');

        switch ($type) {
            case 'import':
                if (preg_match($this->protocol_pattern, $script)) {
                    $filepath = $script;
                } else {
                    $filepath = base_url().$script;
                }
                $js = '<script type="text/javascript" src="'.$filepath.'"';
                if ($defer) {
                    $js .= ' defer="defer"';
                }
                $js .= '></script>';

                break;

            case 'embed':
                $js = '<script type="text/javascript"';
                if ($defer) {
                    $js .= ' defer="defer"';
                }
                $js .= '>';
                $js .= $script;
                $js .= '</script>';

                break;

            default:
                $success = false;

                break;
        }

        // Add to js array if it doesn't already exist
        if (null != $js && !in_array($js, $this->js)) {
            $this->js[] = $js;
            $this->write('_scripts', $js."\r\n");
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Dynamically include CSS in the template.
     *
     * NOTE: This function does NOT check for existence of .css file
     *
     * @param   string   CSS file to link, import or embed
     * @param   string  'link', 'import' or 'embed'
     * @param   string  media attribute to use with 'link' type only, FALSE for none
     * @param mixed $style
     * @param mixed $type
     * @param mixed $media
     *
     * @return true on success, FALSE otherwise
     */
    public function add_css($style, $type = 'link', $media = false)
    {
        $success = true;
        $css = null;

        $this->CI->load->helper('url');
        if (preg_match($this->protocol_pattern, $style)) {
            $filepath = $style;
        } else {
            $filepath = base_url().$style;
        }

        switch ($type) {
            case 'link':
                $css = '<link type="text/css" rel="stylesheet" href="'.$filepath.'"';
                if ($media) {
                    $css .= ' media="'.$media.'"';
                }
                $css .= ' />';

                break;

            case 'import':
                $css = '<style type="text/css">@import url('.$filepath.');</style>';

                break;

            case 'embed':
                $css = '<style type="text/css">';
                $css .= $style;
                $css .= '</style>';

                break;

            default:
                $success = false;

                break;
        }

        // Add to js array if it doesn't already exist
        if (null != $css && !in_array($css, $this->css)) {
            $this->css[] = $css;
            $this->write('_styles', $css."\r\n");
        }

        return $success;
    }

    // --------------------------------------------------------------------

    /**
     * Render the master template or a single region.
     *
     * @param null|string $region optionally opt to render a specific region
     * @param bool        $buffer FALSE to output the rendered template, TRUE to return as a string. Always TRUE when $region is supplied
     * @param mixed       $parse
     */
    public function render($region = null, $buffer = false, $parse = false)
    {
        // Just render $region if supplied
        if ($region) { // Display a specific regions contents
            if (isset($this->regions[$region])) {
                $output = $this->_build_content($this->regions[$region]);
            } else {
                show_error("Cannot render the '{$region}' region. The region is undefined.");
            }
        }

        // Build the output array
        else {
            foreach ($this->regions as $name => $region) {
                $this->output[$name] = $this->_build_content($region);
            }

            if (true === $this->parse_template or true === $parse) {
                // Use provided parser class and method to render the template
                $output = $this->CI->{$this->parser}->{$this->parser_method}($this->master, $this->output, true);

                // Parsers never handle output, but we need to mimick it in this case
                if (false === $buffer) {
                    $this->CI->output->set_output($output);
                }
            } else {
                // Use CI's loader class to render the template with our output array
                $output = $this->CI->load->view($this->master, $this->output, $buffer);
            }
        }

        return $output;
    }

    // --------------------------------------------------------------------

    /**
     * Load the master template or a single region.
     *
     * DEPRECATED!
     *
     * Use render() to compile and display your template and regions
     *
     * @param null|mixed $region
     * @param mixed      $buffer
     */
    public function load($region = null, $buffer = false)
    {
        $region = null;
        $this->render($region, $buffer);
    }

    // --------------------------------------------------------------------

    /**
     * Get the url to the certain selecting template folder in asset folder.
     *
     * @return string
     */
    public function template_url()
    {
        $CI = &get_instance();
        $CI->load->helper('url');

        return base_url().$this->asset_fld.'/'.$this->folder;
    }

    // --------------------------------------------------------------------

    /*
     * Set the view mode (web or mobile) manual by cookie
     *
     * @access  public
     * @param   void
     * @return  void
     */

    public function set_viewmode($viewmode = null)
    {
        if (null === $viewmode) {
            $viewmode = $this->CI->input->get('template_viewmode');
        }

        if (null !== $viewmode && in_array($viewmode, ['web', 'mobile'])) {
            $cookie = [
                'name' => 'viewmode',
                'prefix' => 'template_',
                'value' => $viewmode,
                'expire' => 31536000,   // 1 năm
            ];
            $this->CI->input->set_cookie($cookie);
        } elseif (-1 == $viewmode) {
            $cookie = [
                'name' => 'viewmode',
                'prefix' => 'template_',
                'expire' => -1,
            ];
            $this->CI->input->set_cookie($cookie);
        }
    }

    // --------------------------------------------------------------------

    /*
     * Check if viewmode is mobile or not.
     *
     * This function will first check manual viewmode set by cookie.
     * If manual viewmode has not been set, it will check by using user_agent library
     *
     * If you turn $change_viewmode on, this function only return true
     * if both viewmode is mobile and the mobile_template is already set in config file.
     *
     * @access  public
     * @param   boolean : automatically change to mobile viewmode if is mobile or not
     * @return  void
     */

    public function is_mobile($change_viewmode = false)
    {
        $is_mobile = $this->CI->agent->is_mobile();

        // Xét xem có quy định viewmode thủ công hay không
        // Cookie tuy được set tại connection này nhưng phải đến connection sau mới
        // có hiệu lực, nên phải dùng get để check cho connection hiện tại
        $viewmode = $this->CI->input->get('template_viewmode');
        if (null === $viewmode || !in_array($viewmode, ['web', 'mobile', '-1'])) {
            $viewmode = $this->CI->input->cookie('template_viewmode');
        }

        switch ($viewmode) {
            case 'web':
                $is_mobile = false;

                break;

            case 'mobile':
                $is_mobile = true;

                break;
        }

        // Xét xem có quy định theme cho mobile hay không
        if (true === $change_viewmode && empty($this->config['mobile_template'])) {
            $is_mobile = false;
        }

        change_viewmode:
        if (true === $is_mobile && true === $change_viewmode) {
            $this->set_template($this->config['mobile_template']);
        }

        return $is_mobile;
    }

    // --------------------------------------------------------------------

    /**
     * Build a region from it's contents. Apply wrapper if provided.
     *
     * @param mixed  $region     region to build
     * @param string $wrapper    HTML element to wrap regions in; like '<div>'
     * @param array  $attributes Multidimensional array of HTML elements to apply to $wrapper
     *
     * @return string Output of region contents
     */
    protected function _build_content($region, $wrapper = null, $attributes = null)
    {
        $output = null;

        // Can't build an empty region. Exit stage left
        if (!isset($region['content']) or !count($region['content'])) {
            return false;
        }

        // Possibly overwrite wrapper and attributes
        if ($wrapper) {
            $region['wrapper'] = $wrapper;
        }
        if ($attributes) {
            $region['attributes'] = $attributes;
        }

        // Open the wrapper and add attributes
        if (isset($region['wrapper'])) {
            // This just trims off the closing angle bracket. Like '<p>' to '<p'
            $output .= substr($region['wrapper'], 0, strlen($region['wrapper']) - 1);

            // Add HTML attributes
            if (isset($region['attributes']) && is_array($region['attributes'])) {
                foreach ($region['attributes'] as $name => $value) {
                    // We don't validate HTML attributes. Imagine someone using a custom XML template..
                    $output .= " {$name}=\"{$value}\"";
                }
            }

            $output .= '>';
        }

        // Output the content items.
        foreach ($region['content'] as $content) {
            $output .= $content;
        }

        // Close the wrapper tag
        if (isset($region['wrapper'])) {
            // This just turns the wrapper into a closing tag. Like '<p>' to '</p>'
            $output .= str_replace('<', '</', $region['wrapper'])."\n";
        }

        return $output;
    }
}
// END Template Class

// End of file Template.php
// Location: ./system/application/libraries/Template.php
