<?php

require_once 'controllers/User/AbstractController.php';

class Elm_CommentController extends Elm_User_AbstractController
{
	/**
	 * Pre Dispatch check for invalid session
	 */
	public function preDispatch()
	{
        parent::preDispatch();

        $action = $this->getRequest()->getActionName();
        $pattern = '/^(image|view)/i';
        if (!preg_match($pattern, $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                //$this->_redirect('/user/login');
            }
        }
	}

	/**
	 * view action
	 */
	public function indexAction()
	{
		$this->_forward('view');
	}

	/**
	 * view action
	 *
	 * @TODO: view individual post action
	 * @see http://twitter.com/#!/cbourdage/status/159422562189840385
	 */
	public function viewAction()
	{
		$id = $this->getRequest()->getParam('id');
		if ($plot = Elm::getModel('plot')->load($id)) {
			Zend_Registry::set('current_plot', $plot);
			$this->view->plot = $plot;
			$this->view->headTitle()->prepend($plot->getName());
		} else {
			$this->_forward('no-route');
		}
		$this->_initLayout();
	}

	/**
	 * Create new status message action
	 */
	public function createAction()
	{
		$response = array();
		$session = $this->_getSession();

		//if ($this->getRequest()->getParam('isAjax', true)) {
			$this->getHelper()->layout()->disableLayout();
			$this->getHelper()->viewRenderer->setNoRender(true);
		//}

        if (!$session->isLoggedIn()) {
			$response = array(
				'success' => false,
				'error' => true,
				'location' => $this->_helper->url('user/login')
			);
        } else {
			if ($this->getRequest()->isPost()) {
				//Elm::log($this->getRequest()->getPost());
				$comment = Elm::getModel('plot/status')->setData($this->getRequest()->getPost());
				if ($comment->isValid()) {
					try {
						$comment->save();
						//$status->sendNewStatusUpdate();
						// Temporary date set
						$comment->setCreatedAt(date('U'));
						$response = array(
							'success' => true,
							'error' => false,
							'html' => $this->view->partial('plot/status/item.phtml', array('item' => $comment))
							//'html' => '<li><h4>' . $comment->getUser()->getName() . '</h4><p>' . $comment->getContent() . '</p></li>'
						);
					} catch (Exception $e) {
						$response = array(
							'success' => false,
							'error' => true,
							'message' => $e->getMessage()
						);
					}
				} else {
					$response = array(
						'success' => false,
						'error' => true,
						'message' =>'Oops! Check required fields and try again.'
					);
				}
			}
		}

		$this->_helper->json->sendJson($response);
	}


	/**
	 * Create new status message action
	 */
	public function rateItAction()
	{
		$response = array();
		$session = $this->_getSession();

		//if ($this->getRequest()->getParam('isAjax', true)) {
			$this->_helper->layout()->disableLayout();
			$this->_helper->viewRenderer->setNoRender(true);
		//}

        if (!$session->isLoggedIn()) {
			$response = array(
				'success' => false,
				'error' => true,
				'location' => $this->_helper->url('user/login')
			);
        } else {
			// @TODO finish the rating system
			$comment = Elm::getModel('plot/status')->load($this->getRequest()->getParam('comment_id'));
			Elm::log('rating: ' . $comment->getId());
			//$comment->rate($this->getRequest()->getParam('rating'));
			$response = array(
				'success' => true,
				'error' => false,
				'message' => 'Successfully rated'
			);
		}

		$this->_helper->json->sendJson($response);
	}
}