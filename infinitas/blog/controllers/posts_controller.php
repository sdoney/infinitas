<?php
/**
* Blog Posts Controller class file.
*
* This is the main controller for all the blog posts.  It extends
* {@see BlogAppController} for some functionality.
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
* @subpackage blog.controllers.posts
* @license http://www.opensource.org/licenses/mit-license.php The MIT License
* @since 0.5a
*/
class PostsController extends BlogAppController {
	/**
	* Class name.
	*
	* @access public
	* @var string
	*/
	var $name = 'Posts';

	/**
	* Helpers.
	*
	* @access public
	* @var array
	*/
	var $helpers = array(
		'Libs.Geshi',
		'Filter.Filter'
		);

	/**
	* PostsController::beforeFilter()
	*
	* empty
	*/
	function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	* Index for users
	*
	* @param string $tag used to find posts with a tag
	* @param string $year used to find posts in a cetain year
	* @param string $month used to find posts in a year and month needs year
	* @return
	*/
	function index($tag = null, $year = null, $month = null) {
		$post_ids = '';
		if (isset($tag) && strtolower($tag) != 'all') {
			$post_ids = $this->Post->Tag->findPostsByTag($tag);
		}

		$this->paginate = array(
			'fields' => array(
				'Post.id',
				'Post.title',
				'Post.slug',
				'Post.body',
				'Post.intro',
				'Post.comment_count',
				'Post.created',
				),
			'conditions' => array(
				'Post.active' => 1,
				'Post.id' . ((!empty($post_ids)) ? ' IN (' . implode(',', $post_ids) . ')' : ' > 0')
				),
			'contain' => array(
				'Tag' => array(
					'fields' => array(
						'Tag.name'
						)
					),
				'Comment' => array(
					'conditions' => array(
						'Comment.active' => 1
						)
					)
				)
			);

		$posts = $this->paginate('Post');
		$this->set(compact('posts'));
	}

	/**
	* User view
	*
	* @param string $slug the slug for the record
	* @return na
	*/
	function view($slug = null) {
		if (!$slug) {
			$this->Session->setFlash('That post could not be found', true);
			$this->redirect($this->referer());
		}

		$post = $this->Post->find(
			'first',
			array(
				'fields' => array(
					'Post.id',
					'Post.title',
					'Post.slug',
					'Post.intro',
					'Post.body',
					'Post.active',
					'Post.views',
					'Post.comment_count',
					'Post.rating',
					'Post.rating_count',
					'Post.created',
					'Post.modified'
					),
				'conditions' => array(
					'or' => array(
						'Post.slug' => $slug,
						'Post.id' => $slug
						),
					'Post.active' => 1
					),
				'contain' => array(
					'Comment' => array(
						'fields' => array(
							'Comment.id',
							'Comment.name',
							'Comment.email',
							'Comment.website',
							'Comment.comment',
							'Comment.created'
							),
						'conditions' => array(
							'Comment.active' => 1
							)
						)
					)
				)
			);

		/**
		* make sure there is something found and the post is active
		*/
		if (empty($post) || !$post['Post']['active']) {
			$this->Session->setFlash('No post was found', true);
			$this->redirect($this->referer());
		}

		$this->set(compact('post'));
		$this->set('title_for_layout', $post['Post']['slug']);
	}

	/**
	* Admin Section.
	*
	* All the admin methods.
	*/
	/**
	* Admin dashboard
	*
	* @return na
	*/
	function admin_dashboard() {
		$feed = $this->Post->find(
			'feed',
			array(
				'setup' => array(
					'plugin' => 'Blog',
					'controller' => 'posts',
					'action' => 'view',
					),
				'fields' => array(
					'Post.id',
					'Post.title',
					'Post.intro',
					'Post.created'
					),
				'feed' => array(
					'Core.Comment' => array(
						'setup' => array(
							'plugin' => 'Comment',
							'controller' => 'comments',
							'action' => 'view',
							),
						'fields' => array(
							'Comment.id',
							'Comment.name',
							'Comment.comment',
							'Comment.created'
							)
						)
					),
				'order' => array(
					'created' => 'DESC'
					)
				)
			);

		$this->set('blogFeeds', $feed);

		$this->set('dashboardPostCount', $this->Post->getCounts());
		$this->set('dashboardPostLatest', $this->Post->getLatest());
		$this->set('dashboardCommentsCount', $this->Post->Comment->getCounts('Blog.Post'));
	}

	/**
	* Admin index.
	*
	* Uses the {@see FilterComponent} component to filter results.
	*
	* @return na
	*/
	function admin_index() {
		$this->Post->recursive = 0;
		$posts = $this->paginate(null, $this->Filter->filter);

		$filterOptions = $this->Filter->filterOptions;
		$filterOptions['fields'] = array(
			'title',
			'body'
		);

		$this->set(compact('posts','filterOptions'));
	}

	/**
	* Admin add.
	*
	* This does some trickery for creating tags from the textarea comma
	* delimited. also makes sure there are no duplicates created.
	*
	* @return na
	*/
	function admin_add() {
		if (!empty($this->data)) {
			if (isset($this->data['Post']['new_tags'])) {
				$new_tags = explode(',', $this->data['Post']['new_tags']);

				foreach($new_tags as $new_tag) {
					$tag['Tag']['name'] = ucfirst(strtolower(trim($new_tag)));
					$this->Post->Tag->create();
					if ($this->Post->Tag->save($tag)) {
						$this->data['Tag']['Tag'][] = $this->Post->Tag->id;
						// unset( $this->Post->Tag->id );
					}

					unset($tag);
				}
			}

			$this->Post->create();
			if ($this->Post->saveAll($this->data)) {
				$this->Session->setFlash('Your post has been saved.');
				$this->redirect(array('action' => 'index'));
			}
		}

		$tags = $this->Post->Tag->find('list');
		$this->set(compact('tags'));
	}

	function admin_edit($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('That post could not be found', true), true);
			$this->redirect($this->referer());
		}

		if (!empty($this->data)) {
			if (isset($this->data['Post']['new_tags'])) {
				$new_tags = explode(',', $this->data['Post']['new_tags']);

				foreach($new_tags as $new_tag) {
					$tag['Tag']['name'] = ucfirst(strtolower(trim($new_tag)));
					$this->Post->Tag->create();
					if ($this->Post->Tag->save($tag)) {
						$this->data['Tag']['Tag'][] = $this->Post->Tag->id;
						// unset( $this->Post->Tag->id );
					}

					unset($tag);
				}
			}

			if ($this->Post->saveAll($this->data)) {
				$this->Session->setFlash(__('Your post has been saved.', true));
				$this->redirect(array('action' => 'index'));
			}

			$this->Session->setFlash(__('Your post could not be saved.', true));
		}

		if ($id && empty($this->data)) {
			$this->data = $this->Post->lock(null, $id);
			if ($this->data === false) {
				$this->Session->setFlash(__('The post is currently locked', true));
				$this->redirect($this->referer());
			}
		}

		$tags = $this->Post->Tag->find('list');
		$this->set(compact('tags'));
	}

	function admin_view($slug = null) {
		if (!$slug) {
			$this->Session->setFlash('That post could not be found', true);
			$this->redirect($this->referer());
		}

		$post = ((int)$slug > 0)
		? $this->Post->read(null, $slug)
		: $this->Post->find(
			'first',
			array(
				'conditions' => array(
					'Post.slug' => $slug
					)
				)
			);

		$this->set(compact('post'));
		$this->render('view');
	}

	function admin_delete() {
		return false;
	}
}

?>