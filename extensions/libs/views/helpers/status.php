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

    class StatusHelper extends AppHelper
    {
        var $helpers = array( 'Html', 'Time', 'Libs.Image' );

        var $external = true;

        function status( $status = null )
        {
            $image = false;
            $params = array();

            switch ( strtolower( $status ) )
            {
                case 1:
                case 'yes':
                case 'on':
                    if ( $this->external )
                    {
                        $params['title'] = __( 'Active', true );
                    }

                    $image = $this->Html->image(
                        $this->Image->getRelativePath( 'status', 'active' ),
                        $params + array(
                            'width' => '16px',
                            'alt' => __( 'On', true )
                        )
                    );
                    break;

                case 0:
                case 'no':
                case 'off':
                    if ( $this->external )
                    {
                        $params['title'] = __( 'Disabled', true );
                    }

                    $image = $this->Html->image(
                        $this->Image->getRelativePath( 'status', 'inactive' ),
                        $params + array(
                            'width' => '16px',
                            'alt' => __( 'Off', true )
                        )
                    );
                    break;
            }

            return $image;
        }

        /**
        * Toogle button
        *
        * Uses Status::status to get the image and then creates a link based on
        * the $method param
        */
        function toggle( $status = null, $id = null, $url = array( 'action' => 'toggle' ) )
        {
            $params = array();

            switch( $status )
            {
                case 0:
                case 'off':
                case 'no':
                    $params['title'] = __( 'Click to activate', true );
                    $params['alt'] = __( 'Disabled', true );
                    break;

                case 1:
                case 'yes':
                case 'on':
                    $params['title'] = __( 'Click to disable', true );
                    $params['alt'] = __( 'Active', true );
                    break;
                default:
                    ;
            } // switch

            $this->external = false;

            $link = $this->Html->link(
                $this->status( $status ),
                (array)$url + (array)$id
                ,
                $params + array(
                    'escape' => false
                )
            );

            return $link;
        }

        function locked( $item = array(), $model = null )
        {
            if ( !$model || empty( $item ) || empty( $item[$model] ) )
            {
                $this->errors[] = 'you missing some data there.';
                return false;
            }

            switch ( strtolower( $item[$model]['locked'] ) )
            {
                case 1:
                    $image = $this->Html->image(
                        $this->Image->getRelativePath( 'status', 'locked' ),
                        array(
                            'alt' => __( 'Locked', true ),
                            'width' => '16px',
                            'title' => sprintf(
                                __( 'This record was locked %s by %s', true ),
                                $this->Time->timeAgoInWords( $item[$model]['locked_since'] ),
                                $item['Locker']['username'] )
                        )
                    );
                    break;

                case 0:
                    $image = $this->Html->image(
                        $this->Image->getRelativePath( 'status', 'not-locked' ),
                        array(
                            'alt' => __( 'Not Locked', true ),
                            'width' => '16px',
                            'title' => __( 'This record is not locked', true )
                        )
                    );
                    break;
            }

            return $image;
        }

        function featured( $record = array(), $model = 'Feature' )
        {
            if ( empty( $record[$model] ) )
            {
                $this->messages[] = 'This has no featured items.';

                return $this->Html->image(
                    $this->Image->getRelativePath( 'status', 'not-featured' ),
                    array(
                        'alt'   => __( 'No', true ),
                        'title' => __( 'Not a featured item', true ),
                        'width' => '16px'
                    )
                );
            }

            return $this->Html->image(
                $this->Image->getRelativePath( 'status', 'featured' ),
                array(
                    'alt'   => __( 'Yes', true ),
                    'title' => __( 'Featured Item', true ),
                    'width' => '16px'
                )
            );
        }
    }
?>