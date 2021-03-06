<?php
    /**
     * Comment Template.
     *
     * @todo -c Implement .this needs to be sorted out.
     *
     * Copyright (c) 2009 Carl Sutton ( dogmatic69 )
     *
     * Licensed under The MIT License
     * Redistributions of files must retain the above copyright notice.
     *
     * @filesource
     * @copyright     Copyright (c) 2009 Carl Sutton ( dogmatic69 )
     * @link          http://www.dogmatic.co.za
     * @package       sort
     * @subpackage    sort.comments
     * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
     * @since         0.5a
     */
?>
<div id="comment-box">
	<?php
        /**
         * fields allowed in the comments
         */
        $commentFields = explode(',',Configure::read('Comment.fields'));

	    $action = ( isset( $action ) ) ? $action : 'comment';
        $modelName = ( isset( $modelName ) ) ? $modelName : Inflector::singularize( $this->name );

        if ( isset( $urlParams ) )
        {
            echo $this->Form->create(
                $modelName,
                array(
                	'url' => array(
                		'plugin' => $this->params['plugin'],
                		'controller' => $this->params['controller'],
                		'action' => $action,
                		$urlParams
                	)
                )
            );
        }

        else
        {
            echo $this->Form->create(
                $modelName,
                array(
                	'url' => array(
                		'plugin' => $this->params['plugin'],
                		'controller' => $this->params['controller'],
                		'action' => $action
                	)
                )
            );
        }
    ?>
    <fieldset>
        <legend><?php __( "Post a {$commentModel}" );?></legend>
        <?php
            echo $this->Form->input( "$modelName.id", array( 'value' => $fk ) );

            foreach( $commentFields as $field )
            {
                if ( $field != 'comment' )
                {
                    echo $this->Form->input( 'Comment.'.$field );
                }
                else
                {
                    echo $this->Blog->wysiwyg( 'Comment.comment', 'Simple' );
                }
            }
        ?>
    </fieldset>
	<?php echo $this->Form->end('Submit'); ?>
</div>