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
    echo $this->Form->create( 'Category', array( 'url' => array( 'controller' => 'categories', 'action' => 'mass', 'admin' => 'true' ) ) );
        $massActions = $this->Cms->massActionButtons(
            array(
                'add',
                'edit',
                'preview',
                'toggle',
                'copy',
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
                    $this->Paginator->sort( 'title' ),
                    $this->Paginator->sort( 'Parent', 'Section.title' ),
                    $this->Paginator->sort( 'Group', 'Group.name' ) => array(
                        'style' => 'width:100px;'
                    ),
                    $this->Paginator->sort( 'Items', 'content_count' ) => array(
                        'style' => 'width:35px;'
                    ),
                    $this->Paginator->sort( 'views' ) => array(
                        'style' => 'width:40px;'
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
            foreach ( $categories as $category )
            {
                ?>
                	<tr class="<?php echo $this->Cms->rowClass(); ?>">
                        <td><?php echo $this->Form->checkbox( $category['Category']['id'] ); ?>&nbsp;</td>
                		<td>
                			<?php echo $this->Html->link( $category['Category']['title'], array('action' => 'edit', $category['Category']['id'] ) ); ?>
                		</td>
                		<td>
                			<?php echo $this->Html->link( $category['Parent']['title'], array('controller' => 'categories', 'action' => 'edit', $category['Parent']['id'] ) ); ?>
                		</td>
                		<td>
                			<?php echo $category['Group']['name']; ?>
                		</td>
                		<td style="text-align:center;">
                			<?php echo $category['Category']['content_count']; ?>
                		</td>
                		<td style="text-align:center;">
                			<?php echo $category['Category']['views']; ?>
                		</td>
                		<td>
                			<?php echo $this->Time->niceShort( $category['Category']['modified'] ); ?>
                		</td>
                		<td>
                			<?php
                			    // @todo -c Implement .add up and down for mptt
                			?>
                		</td>
                		<td>
                			<?php
                			    echo $this->Status->toggle( $category['Category']['active'], $category['Category']['id'] ),
                    			    $this->Status->locked( $category, 'Category' );
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