<?php
namespace ORFW\Front;

class ReviewUI
{
    public static $instance;

    public static function getInstance()
    {
        if ( !self::$instance instanceof self )
            self::$instance = new self();
        
        return self::$instance;
    }

    private function __construct()
    {
        add_action( 'woocommerce_review_after_comment_text', array( $this, 'afterBal' ) );
    }

    public function afterBal( $comment )
    {
        $commentID = intval($comment->comment_ID);
    ?>
    <div class="orfw-review-des">
        <small>This user bought w, x, y and z other items.</small>
    </div>
    <?php
    }
}
