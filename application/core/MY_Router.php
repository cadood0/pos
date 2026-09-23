<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Router extends CI_Router
{
    protected function _set_default_controller()
    {
        if (empty($this->default_controller)) {
            show_error('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
        }

        $segments = explode('/', $this->default_controller);
        $path = APPPATH.'controllers/';

        if (isset($segments[0]) && is_dir($path.$segments[0])) {
            $this->set_directory(array_shift($segments));
        }

        $class = isset($segments[0]) ? $segments[0] : NULL;
        $method = isset($segments[1]) ? $segments[1] : 'index';

        if (empty($class) || ! file_exists($path.$this->directory.ucfirst($class).'.php')) {
            return;
        }

        $this->set_class($class);
        $this->set_method($method);

        $this->uri->rsegments = [
            1 => $class,
            2 => $method,
        ];

        log_message('debug', 'No URI present. Default controller set.');
    }
}
