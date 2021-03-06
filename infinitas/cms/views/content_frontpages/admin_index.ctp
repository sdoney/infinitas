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

    echo $this->Form->create( 'ContentFrontpage', array( 'url' => array( 'controller' => 'contentFrontpages', 'action' => 'mass', 'admin' => 'true' ) ) );
        $massActions = $this->Cms->massActionButtons(
            array(
                'add',
                'delete'
            )
        );
        echo $this->Cms->adminIndexHead( $this, $paginator, $filterOptions, $massActions );

?>
<div class="table">
    <?php echo $this->Cms->adminTableHeadImages(); ?>
    <table class="listing" cellpadding="0" cellspacing="0">
        <?php
            echo $this->Cms->adminTableHeader(
                array(
                    $this->Form->checkbox( 'all' ) => array(
                        'class' => 'first',
                        'style' => 'width:25px;'
                    ),
                    $this->Paginator->sort( 'Content Item', 'Content.title' ),
                    __( 'Category', true ),
                    $this->Paginator->sort( 'created' ) => array(
                        'style' => 'width:100px;'
                    ),
                    $this->Paginator->sort( 'modified' ) => array(
                        'style' => 'width:100px;'
                    ),
                    $this->Paginator->sort( 'ordering' ) => array(
                        'style' => 'width:50px;'
                    ),
                    __( 'Status', true ) => array(
                        'style' => 'width:50px;'
                    )
                )
            );

            $i = 0;
            foreach ( $contentFrontpages as $contentFrontpage )
            {
                ?>
                	<tr class="<?php echo $this->Cms->rowClass( $i ); ?>">
                        <td><?php echo $this->Form->checkbox( $contentFrontpage['ContentFrontpage']['id'] ); ?>&nbsp;</td>
                		<td>
                			<?php echo $this->Html->link( $contentFrontpage['Content']['title'], array('controller' => 'contents', 'action' => 'view', $contentFrontpage['Content']['id'])); ?>
                		</td>
                		<td>
                			<?php echo $this->Html->link( $contentFrontpage['Content']['Category']['title'], array( 'controller' => 'categories', 'action' => 'edit', $contentFrontpage['Content']['Category']['id'] ) ); ?>
                		</td>
                		<td>
                			<?php echo $this->Time->niceShort( $contentFrontpage['ContentFrontpage']['created'] ); ?>
                		</td>
                		<td>
                			<?php echo $this->Time->niceShort( $contentFrontpage['ContentFrontpage']['modified'] ); ?>
                		</td>
                		<td>
                			<?php
                			    echo $this->Cms->ordering(
                			        $contentFrontpage['ContentFrontpage']['content_id'],
                			        $contentFrontpage['ContentFrontpage']['ordering']
                			    );
                			?>
                		</td>
                		<td>
                			<?php
                			    echo $this->Status->toggle( $contentFrontpage['Content']['active'], $contentFrontpage['Content']['id'], array( 'controller' => 'contents', 'action' => 'toggle' ) );
                			?>
                		</td>
                	</tr>
                <?php
            }
        ?>
    </table>
    <?php
        echo $this->Form->end();

    ?>
</div>
<?php echo $this->element( 'pagination/navigation' ); ?>