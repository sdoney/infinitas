<?php
/**
* Blog Tag Model class file.
*
* This is the main model for Blog Tags.
*
* Copyright (c) 2009 Carl Sutton ( dogmatic69 )
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @filesource
* @copyright Copyright (c) 2009 Carl Sutton ( dogmatic69 )
* @link http://www.dogmatic.co.za
* @package blog
* @subpackage blog.models.tag
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
*/
class Tag extends BlogAppModel {
	var $name = 'Tag';

	var $order = array(
		'Tag.name' => 'ASC'
	);

	var $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Please enter a tag'
			),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'That tag already exists'
			)
		)
	);

	var $hasAndBelongsToMany = array(
		'Post' =>
		array(
			'className' => 'Blog.Post',
			'joinTable' => 'blog_posts_tags',
			'foreignKey' => 'tag_id',
			'associationForeignKey' => 'post_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);

	function getCount($limit = 50) {
		$tags = Cache::read('tag_count');

		if ($tags !== false) {
			return $tags;
		}

		$tags = $this->find(
			'all',
			array(
				'fields' => array(
					'Tag.id',
					'Tag.name'
					),
				'contain' => array(
					'Post' => array(
						'fields' => array(
							'Post.id'
							)
						)
					),
				'limit' => $limit
				)
			);

		foreach($tags as $k => $tag) {
			$tags[$k]['Tag']['count'] = count($tag['Post']);
			unset($tags[$k]['Post']);
		}

		Cache::write('tag_count', $tags, 'blog');

		return $tags;
	}

	function findPostsByTag($tag) {
		$tags = $this->find(
			'all',
			array(
				'conditions' => array(
					'or' => array(
						'Tag.id' => $tag,
						'Tag.name' => $tag
						)
					),
				'fields' => array(
					'Tag.id'
					),
				'contain' => array(
					'Post' => array(
						'fields' => array(
							'Post.id'
							)
						)
					)
				)
			);

		return Set::extract('/Post/id', $tags);
	}
}

?>