<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package CodeIgniter
 * @author  ExpressionEngine Dev Team
 * @copyright  Copyright (c) 2006, EllisLab, Inc.
 * @license http://codeigniter.com/user_guide/license.html
 * @link http://codeigniter.com
 * @since   Version 1.0
 * @filesource
 */

// --------------------------------------------------------------------

/**
 * CodeIgniter Template Class
 *
 * This class is and interface to CI's View class. It aims to improve the
 * interaction between controllers and views. Follow @link for more info
 *
 * @package		CodeIgniter
 * @author		Colin Williams
 * @subpackage	Libraries
 * @category	Libraries
 * @link		http://www.williamsconcepts.com/ci/libraries/template/index.html
 * @copyright  Copyright (c) 2008, Colin Williams.
 * @version 1.4.1
 * 
 */
class Template {
   
    var $CI;
    var $config;
    var $template;
    var $master;
    var $folder;
    var $asset_fld = 'asset'; // path to the folder which contains templates' CSS, javascript... files, relative to Front Controller
    var $default_arguments = array(); // 
    var $output;
    var $regions = array();
    var $default_regions = array(
        '_scripts' => array(),
        '_styles' => array(),
    );
    var $js = array();
    var $css = array();
    var $parser = 'parser';
    var $parser_method = 'parse';
    var $parse_template = FALSE;
    var $protocol_pattern = '{^http://|^https://|^ftp://}';
   
   /**
	 * Constructor
	 *
	 * Loads template configuration, template regions, and validates existence of 
	 * default template
	 *
	 * @access	public
	 */
   
    public function __construct()
    {
        // Copy an instance of CI so we can use the entire framework.
        $this->CI =& get_instance();
        $this->CI->load->library('user_agent');
        
        // Load the template config file and setup our master template and regions
        include(APPPATH.'config/template'.'.php');
        if (isset($template))
        {
            $this->config = $template;
            $this->set_template($template['active_template']);
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Use given template settings
    *
    * @access  public
    * @param   string   array key to access template settings
    * @return  void
    */
   
    public function set_template($group)
    {
        if (isset($this->config[$group]))
        {
            $this->folder = $group . '/'; //By default, the Template Folder is the Template group name
            $this->template = $this->config[$group];
        }
        else
        {
            show_error('The "'. $group .'" template group does not exist. Provide a valid group name or add the group first.');
        }
        $this->initialize($this->template);
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Set master template
    *
    * @access  public
    * @param   string   filename of new master template file
    * @return  void
    */
   
    public function set_master_template($filename)
    {  
        $filepath = $this->folder . $filename;
        if (file_exists(APPPATH .'views/'. $filepath) or file_exists(APPPATH .'views/'. $filepath . '.php'))
        {
            $this->master = $filepath;
        }
        else
        {
            show_error('The filename provided does not exist in <strong>'. APPPATH .'views</strong>. Remember to include the extension if other than ".php"');
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Dynamically add a template and optionally switch to it
    *
    * @access  public
    * @param   string   array key to access template settings
    * @param   array properly formed
    * @return  void
    */
   
    public function add_template($group, $template, $activate = FALSE)
    {
        if ( ! isset($this->config[$group]))
        {
            $this->config[$group] = $template;
            if ($activate === TRUE)
            {
                $this->initialize($template);
            }
        }
        else
        {
            show_error('The "'. $group .'" template group already exists. Use a different group name.');
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Initialize class settings using config settings
    *
    * @access  public
    * @param   array   configuration array
    * @return  void
    */
   
    public function initialize($props)
    {
        // Set Template Folder
        if (isset($props['folder']))
        {  
            if (trim($props['folder']) !== '')
                $this->folder = $props['folder'] . '/';
            else
                $this->folder = '';
        }
        
        // Set master template
        if (isset($props['template']))
        {
            $this->set_master_template($props['template']);
        }
        else 
        {
            // Master template must exist. Throw error.
            show_error('Either you have not provided a master template or the one provided does not exist in <strong>'. APPPATH .'views</strong>. Remember to include the extension if other than ".php"');
        }
        
        // Load default arguments
        if (isset($props['default_args']))
        {
            $this->set_default_args($props['default_args']);
        }
        
        // Load our regions
        if (isset($props['regions']))
        {
            $this->set_regions($props['regions']);
        }
        
        // Set parser and parser method
        if (isset($props['parser']))
        {
            $this->set_parser($props['parser']);
        }
        if (isset($props['parser_method']))
        {
            $this->set_parser_method($props['parser_method']);
        }
        
        // Set master template parser instructions
        $this->parse_template = isset($props['parse_template']) ? $props['parse_template'] : FALSE;
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Set regions for writing to
    *
    * @access  public
    * @param   array   properly formed regions array
    * @return  void
    */
   
    public function set_regions($regions)
    {    
        if (count($regions))
        {
            // Reset existed $regions to default
            $this->regions = $this->default_regions;
            
            foreach ($regions as $key => $region) 
            {
                // Regions must be arrays, but we take the burden off the template 
                // developer and insure it here
                if ( ! is_array($region))
                {
                $this->add_region($region);
                }
                else {
                $this->add_region($key, $region);
                }
            }
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Set default arguments that be used cross views
    *
    * @access  public
    * @param   array   properly formed arguments array
    * @return  void
    */
    public function set_default_args($default_args)
    {
        if (is_array($default_args))
        {
            $this->CI->load->vars($default_args);
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Dynamically add region to the currently set template
    *
    * @access  public
    * @param   string   Name to identify the region
    * @param   array Optional array with region defaults
    * @return  void
    */
   
    public function add_region($name, $props = array())
    {
        if ( ! is_array($props))
        {
            $props = array();
        }
        
        if ( ! isset($this->regions[$name]))
        {
            $this->regions[$name] = $props;
        }
        else
        {
            show_error('The "'. $name .'" region has already been defined.');
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Empty a region's content
    *
    * @access  public
    * @param   string   Name to identify the region
    * @return  void
    */
   
    public function empty_region($name)
    {
        if (isset($this->regions[$name]['content']))
        {
            $this->regions[$name]['content'] = array();
        }
        else
        {
            show_error('The "'. $name .'" region is undefined.');
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Set parser
    *
    * @access  public
    * @param   string   name of parser class to load and use for parsing methods
    * @return  void
    */
   
    public function set_parser($parser, $method = NULL)
    {
        $this->parser = $parser;
        $this->CI->load->library($parser);
        
        if ($method)
        {
            $this->set_parser_method($method);
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Set parser method
    *
    * @access  public
    * @param   string   name of parser class member function to call when parsing
    * @return  void
    */
   
    public function set_parser_method($method)
    {
        $this->parser_method = $method;
    }

   // --------------------------------------------------------------------
   
   /**
	 * Write contents to a region
	 *
	 * @access	public
	 * @param	string	region to write to
	 * @param	string	what to write
	 * @param	boolean	FALSE to append to region, TRUE to overwrite region
	 * @return	void
	 */
   
    public function write($region, $content, $overwrite = FALSE)
    {
        if (isset($this->regions[$region]))
        {
            if ($overwrite === TRUE) // Should we append the content or overwrite it
            {
                $this->regions[$region]['content'] = array($content);
            } else {
                $this->regions[$region]['content'][] = $content;
            }
        }
        
        // Regions MUST be defined
        else
        {
            show_error("Cannot write to the '{$region}' region. The region is undefined.");
        }
    }
   
   // --------------------------------------------------------------------
   
   /**
	 * Write content from a View to a region. 'Views within views'
	 *
	 * @access	public
	 * @param	string	region to write to
	 * @param	string	view file to use
	 * @param	array	variables to pass into view
	 * @param	boolean	FALSE to append to region, TRUE to overwrite region
	 * @return	void
	 */
   
    public function write_view($region, $view, $data = NULL, $overwrite = FALSE)
    {
        $view = $this->folder . $view;
        $args = func_get_args();
        
        // Get rid of non-views
        unset($args[0], $args[2], $args[3]);
        
        // Do we have more view suggestions?
        if (count($args) > 1)
        {
            foreach ($args as $suggestion)
            {  
                $suggestion = $this->folder . $suggestion;
                if (file_exists(APPPATH .'views/'. $suggestion . '.php') or file_exists(APPPATH .'views/'. $suggestion))
                {
                // Just change the $view arg so the rest of our method works as normal
                $view = $filepath;
                break;
                }
            }
        }
        
        $content = $this->CI->load->view($view, $data, TRUE);
        $this->write($region, $content, $overwrite);
    
    }
   
   // --------------------------------------------------------------------
   
   /**
	 * Load content of a View without writing it to the region of master template
     * This method is basicly the same with CI->load->view, but it will add the 
     * template folder at the begining of viewpath to classify which template is used
	 *
	 * @access	public
	 * @param	string	view file to use
	 * @param	array	variables to pass into view
	 * @param	boolean	type of method for handle output data
	 * @return	string  if method is TRUE, this will return data view into sring
     *          void    if method is FALSE, this will write out data to browser
	 */
   
    public function get_view($view, $data = NULL, $get_by_string = TRUE)
    {
        $view = $this->folder . $view;
        $args = func_get_args();
        
        // Get rid of non-views
        unset($args[0], $args[2], $args[3]);
        
        // Do we have more view suggestions?
        if (count($args) > 1)
        {
            foreach ($args as $suggestion)
            {  
                $suggestion = $this->folder . $suggestion;
                if (file_exists(APPPATH .'views/'. $suggestion . '.php') or file_exists(APPPATH .'views/'. $suggestion))
                {
                // Just change the $view arg so the rest of our method works as normal
                $view = $filepath;
                break;
                }
            }
        }
        
        return $this->CI->load->view($view, $data, $get_by_string);
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Parse content from a View to a region with the Parser Class
    *
    * @access  public
    * @param   string   region to write to
    * @param   string   view file to parse
    * @param   array variables to pass into view for parsing
    * @param   boolean  FALSE to append to region, TRUE to overwrite region
    * @return  void
    */
   
    public function parse_view($region, $view, $data = NULL, $overwrite = FALSE)
    {
        $this->CI->load->library('parser');
        
        $view = $this->folder . $view;
        $args = func_get_args();
        
        // Get rid of non-views
        unset($args[0], $args[2], $args[3]);
        
        // Do we have more view suggestions?
        if (count($args) > 1)
        {
            foreach ($args as $suggestion)
            {  
                $suggestion = $this->folder . $suggestion;
                if (file_exists(APPPATH .'views/'. $suggestion . '.php') or file_exists(APPPATH .'views/'. $suggestion))
                {
                // Just change the $view arg so the rest of our method works as normal
                $view = $suggestion;
                break;
                }
            }
        }
        
        $content = $this->CI->{$this->parser}->{$this->parser_method}($view, $data, TRUE);
        $this->write($region, $content, $overwrite);
    
    }

   // --------------------------------------------------------------------
   
   /**
    * Dynamically include javascript in the template
    * 
    * NOTE: This function does NOT check for existence of .js file
    *
    * @access  public
    * @param   string   script to import or embed
    * @param   string  'import' to load external file or 'embed' to add as-is
    * @param   boolean  TRUE to use 'defer' attribute, FALSE to exclude it
    * @return  TRUE on success, FALSE otherwise
    */
   
    public function add_js($script, $type = 'import', $defer = FALSE)
    {
        $success = TRUE;
        $js = NULL;
        
        $this->CI->load->helper('url');
        
        switch ($type)
        {
            case 'import':
                if (preg_match($this->protocol_pattern, $script))
                {
                    $filepath = $script;
                }
                else 
                {
                    $filepath = base_url() . $script;
                }
                $js = '<script type="text/javascript" src="'. $filepath .'"';
                if ($defer)
                {
                $js .= ' defer="defer"';
                }
                $js .= "></script>";
                break;
            
            case 'embed':
                $js = '<script type="text/javascript"';
                if ($defer)
                {
                $js .= ' defer="defer"';
                }
                $js .= ">";
                $js .= $script;
                $js .= '</script>';
                break;
                
            default:
                $success = FALSE;
                break;
        }
        
        // Add to js array if it doesn't already exist
        if ($js != NULL && !in_array($js, $this->js))
        {
            $this->js[] = $js;
            $this->write('_scripts', $js."\r\n");
        }
        
        return $success;
    }
   
   // --------------------------------------------------------------------
   
   /**
    * Dynamically include CSS in the template
    * 
    * NOTE: This function does NOT check for existence of .css file
    *
    * @access  public
    * @param   string   CSS file to link, import or embed
    * @param   string  'link', 'import' or 'embed'
    * @param   string  media attribute to use with 'link' type only, FALSE for none
    * @return  TRUE on success, FALSE otherwise
    */
   
    public function add_css($style, $type = 'link', $media = FALSE)
    {
        $success = TRUE;
        $css = NULL;
        
        $this->CI->load->helper('url');
        if (preg_match($this->protocol_pattern, $style))
        {
            $filepath = $style;
        }
        else
        {
            $filepath = base_url() . $style;
        }
        
        switch ($type)
        {
            case 'link':
                
                $css = '<link type="text/css" rel="stylesheet" href="'. $filepath .'"';
                if ($media)
                {
                $css .= ' media="'. $media .'"';
                }
                $css .= ' />';
                break;
            
            case 'import':
                $css = '<style type="text/css">@import url('. $filepath .');</style>';
                break;
            
            case 'embed':
                $css = '<style type="text/css">';
                $css .= $style;
                $css .= '</style>';
                break;
                
            default:
                $success = FALSE;
                break;
        }
        
        // Add to js array if it doesn't already exist
        if ($css != NULL && !in_array($css, $this->css))
        {
            $this->css[] = $css;
            $this->write('_styles', $css."\r\n");
        }
        
        return $success;
    }
      
   // --------------------------------------------------------------------
   
   /**
	 * Render the master template or a single region
	 *
	 * @access	public
	 * @param	string	optionally opt to render a specific region
	 * @param	boolean	FALSE to output the rendered template, TRUE to return as a string. Always TRUE when $region is supplied
	 * @return	void or string (result of template build)
	 */
   
    public function render($region = NULL, $buffer = FALSE, $parse = FALSE)
    {
        // Just render $region if supplied
        if ($region) // Display a specific regions contents
        {
            if (isset($this->regions[$region]))
            {
                $output = $this->_build_content($this->regions[$region]);
            }
            else
            {
                show_error("Cannot render the '{$region}' region. The region is undefined.");
            }
        }
        
        // Build the output array
        else
        {
            foreach ($this->regions as $name => $region)
            {
                $this->output[$name] = $this->_build_content($region);
            }
            
            if ($this->parse_template === TRUE or $parse === TRUE)
            {
                // Use provided parser class and method to render the template
                $output = $this->CI->{$this->parser}->{$this->parser_method}($this->master, $this->output, TRUE);
                
                // Parsers never handle output, but we need to mimick it in this case
                if ($buffer === FALSE)
                {
                $this->CI->output->set_output($output);
                }
            }
            else
            {
                // Use CI's loader class to render the template with our output array
                $output = $this->CI->load->view($this->master, $this->output, $buffer);
            }
        }
        
        return $output;
    }
   
    // --------------------------------------------------------------------
   
    /**
     * Load the master template or a single region
     *
     * DEPRECATED!
     * 
     * Use render() to compile and display your template and regions
     */
    
    public function load($region = NULL, $buffer = FALSE)
    {
       $region = NULL;
       $this->render($region, $buffer);
    }
    
    // --------------------------------------------------------------------
   
    /**
     * Get the url to the certain selecting template folder in asset folder
     * 
     * @access  public
     * @return  string 
     */
    
    public function template_url()
    {
        $CI =& get_instance();
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
     
    public function set_viewmode($viewmode=null) 
    {
        if ($viewmode === null)
            $viewmode = $this->CI->input->get('template_viewmode');
        
        if ($viewmode !== null && in_array($viewmode, array('web', 'mobile'))) {
                
            $cookie = array(
                'name'   => 'viewmode',
                'prefix' => 'template_',
                'value'  => $viewmode,
                'expire' => 31536000,   // 1 năm
            );
            $this->CI->input->set_cookie($cookie);
            
        }
        elseif ($viewmode == -1) {
            
            $cookie = array(
                'name'   => 'viewmode',
                'prefix' => 'template_',
                'expire' => -1,
            );
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
    
    public function is_mobile($change_viewmode=false) 
    {
        $is_mobile = $this->CI->agent->is_mobile();
        
        // Xét xem có quy định viewmode thủ công hay không
        // Cookie tuy được set tại connection này nhưng phải đến connection sau mới 
        // có hiệu lực, nên phải dùng get để check cho connection hiện tại
        $viewmode = $this->CI->input->get('template_viewmode');
        if ($viewmode === null || !in_array($viewmode, array('web', 'mobile', '-1')))
            $viewmode = $this->CI->input->cookie('template_viewmode');        
        switch ($viewmode){
            case 'web':
                $is_mobile = false;
                break;
            case 'mobile':
                $is_mobile = true;
                break;
        }
        
        // Xét xem có quy định theme cho mobile hay không
        if ($change_viewmode === true && empty($this->config['mobile_template'])){
            $is_mobile = false;
        }
        
        change_viewmode:
        if ($is_mobile === true && $change_viewmode === true){
            $this->set_template($this->config['mobile_template']);
        }
        
        return $is_mobile;
        
    }
   
    // --------------------------------------------------------------------
   
    /**
	 * Build a region from it's contents. Apply wrapper if provided
	 *
	 * @access	private
	 * @param	string	region to build
	 * @param	string	HTML element to wrap regions in; like '<div>'
	 * @param	array	Multidimensional array of HTML elements to apply to $wrapper
	 * @return	string	Output of region contents
	 */
   
    protected function _build_content($region, $wrapper = NULL, $attributes = NULL)
    {
        $output = NULL;
        
        // Can't build an empty region. Exit stage left
        if ( ! isset($region['content']) or ! count($region['content']))
        {
            return FALSE;
        }
        
        // Possibly overwrite wrapper and attributes
        if ($wrapper)
        {
            $region['wrapper'] = $wrapper;
        }
        if ($attributes)
        {
            $region['attributes'] = $attributes;
        }
        
        // Open the wrapper and add attributes
        if (isset($region['wrapper'])) 
        {
            // This just trims off the closing angle bracket. Like '<p>' to '<p'
            $output .= substr($region['wrapper'], 0, strlen($region['wrapper']) - 1);
            
            // Add HTML attributes
            if (isset($region['attributes']) && is_array($region['attributes']))
            {
                foreach ($region['attributes'] as $name => $value)
                {
                // We don't validate HTML attributes. Imagine someone using a custom XML template..
                $output .= " $name=\"$value\"";
                }
            }
            
            $output .= ">";
        }
        
        // Output the content items.
        foreach ($region['content'] as $content)
        {
            $output .= $content;
        }
        
        // Close the wrapper tag
        if (isset($region['wrapper']))
        {
            // This just turns the wrapper into a closing tag. Like '<p>' to '</p>'
            $output .= str_replace('<', '</', $region['wrapper']) . "\n";
        }
        
        return $output;
    }
   
}
// END Template Class

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */