<?php
namespace ORFW\Front;

class Popup
{
    public static $instance;

    /**
    * Singleton Pattern
    *
    * @return object
    */
    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();

        return self::$instance;
    }

    /**
    * Constructor
    *
    * @return void
    */
    private function __construct()
    {
      add_action( 'wp_footer', array($this, 'view') );
    }

    public function view()
    {
        include_once ORFW_RENDER_FRONT . '/markup/popup-design-1.php';
    }


}
