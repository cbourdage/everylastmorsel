<?php

/**
 * Elm_IndexController
 */
require_once 'controllers/AbstractController.php';

class Elm_IndexController extends Elm_AbstractController
{
	/**
	 * initializes layout for ajax requests
	 */
	protected function _initAjax()
	{
		$this->_helper->layout()->disableLayout();
	}

	/**
	 * @return void
	 */
    public function indexAction()
    {
    }

	public function comingSoonAction()
	{
		$this->_helper->layout()->setLayout('coming-soon');
		$this->view->headLink()->offsetUnset(4);
		$this->view->headLink()->offsetUnset(3);
		$this->view->headLink()->offsetUnset(2);
		$this->view->headLink()->appendStylesheet('/file-bin/css/coming-soon.css');
	}

	public function comingSoonPostAction()
	{
		if ($this->getRequest()->isPost()) {
			/**
			 * @TODO create new model and resource for email signup
			 */
			if (1 == 2) {
				$model = Elm::getModel('signup')->load($this->getRequest()->getParam('email'));

				if (!$model->getEmail()) {
					$model->setEmail($this->getRequest()->getParam('email'))
						->setRegion($this->getRequest()->getParam('region'))
						->setIpAddress(new Zend_Db_Expr('INET_ATON("' . $_SERVER['REMOTE_ADDR'] . '")'));
					$model->save();

					//$model->sendEmail();

					// @TODO move into model
					// body html
					$bodyHtml = <<<HTMLBODY
<body style="color: #222; text-align: center">
<table style="background: #F8F6F4; border-bottom: 2px solid #ccc; color: #666; font-family: arial, sans-serif; font-size: 13px; margin: 0 auto; text-align: left;" width="450">
<tr>
	<td align="center" style="padding: 15px;">
		<a href="http://www.everylastmorsel.com/" title="Every Last Morsel"><img src="http://www.everylastmorsel.com/images/logo.png" title="Every Last Morsel" /></a>
	</td>
</tr>
<tr>
	<td style="padding: 15px;">
		<p>
			<b style="color: #1DA5B1">Thanks for signing up!</b> We'll be sure to send you an invitation when we're ready
			to launch -- until then we'll be toiling away in the toolshed. Be sure you're taking good notes
			in the mean time -- we're excited to learn what you've got growin' on!
		</p>
	</td>
</tr>
<tr>
	<td style="padding: 15px;">
		<ul style="list-style: none; padding-left: 0;">
			<li><a style="color: #1DA5B1;" href="http://www.facebook.com/everylastmorsel" title="Book 'em!">Facebook</a></li>
			<li><a style="color: #1DA5B1;" href="http://everylastmorsel.tumblr.com/" title="Tumble along">Tumblr</a></li>
			<li><a style="color: #1DA5B1;" href="http://www.twitter.com/@everylastmorsel" title="Tweet @Everylastmorsel">Twitter</a></li>
		</ul>
	</td>
</tr>
</table>
</body>
HTMLBODY;
				//<li><a style="color: #1DA5B1;" href="http://www.google.com/everylastmorsel" title="Plus us">Google+</a></li>

					try {
						// send email out
						$mail = new Zend_Mail('utf-8');
						$mail->addTo("", $email);
						$mail->setSubject("Every Last Morsel welcomes you.");
						$mail->setFrom("greetings@everylastmorsel.com", "Every Last Morsel Communication");
						$mail->setBodyHtml($bodyHtml);
						$mail->send();
					} catch (Exception $e) { }
				}

				Elm::getSingleton('user/session')->addSuccess('Thank you! We will notify you of upcoming news.');
			}
		}

		$this->_redirect('/coming-soon/');
	}

	public function initLocationAction()
	{
		$this->_initAjax();
		$response = array();

		if ($location = Elm::getSingleton('session')->location) {
			$response = array(
				'success' => true,
				'error' => false,
				'city' => $location->getCity(),
				'state' => $location->getState(),
				'zip' => $location->getZip()
			);
		} else {
			$request = $this->getRequest();
			if ($request->getParam('lat', false) && $request->getParam('long', false)) {
				$geo = new Elm_Model_Geolocation($request->getParam('lat'), $request->getParam('long'));

				// set into session to reduce the # of calls
				Elm::getSingleton('session')->location = $geo;
				$response = array(
					'success' => true,
					'error' => false,
					'city' => $geo->getCity(),
					'state' => $geo->getState(),
					'zip' => $geo->getZip()
				);
			}
		}

		$this->_helper->json->sendJson($response);
	}

	/**
	 * Help Action
	 *
	 * @return void
	 */
	public function helpAction()
	{
	}

	/**
	 * FAQ page
	 *
	 * @return void
	 */
	public function faqAction()
	{
	}

	/**
	 * Ajax Step 1 to plot the point and store data
	 */
	public function plotPointAction()
	{
		$this->_initAjax();
		//Elm::log($this->getRequest()->getParams());
		Elm::getSingleton('user/session')->plot = array(
			'latitude' => $this->getRequest()->getParam('lat'),
			'longitude' => $this->getRequest()->getParam('long')
		);
		$this->getResponse()->sendResponse();
	}

	/**
	 * Ajax authentication method for overlay
	 */
	public function authenticateAction()
	{
		$this->_initAjax();
		Elm::getSingleton('user/session')->plot['type'] = $this->getRequest()->getParam('type');
		if (Elm::getSingleton('user/session')->isLoggedIn()) {
			if ($this->getRequest()->getParam('type') == 'isA') {
				$this->_forward('garden-details');
			} else {
				$this->_forward('garden-details');
				/*$response = array(
					'success' => true,
					'error' => false,
					'location' => $this->view->url('plot/startup')
				);
				$this->_helper->json->sendJson($response);*/
			}
		} else {
			$loginForm = new Elm_Model_Form_User_Login();
			$loginForm->setAction('/user/login-ajax?type=' . $this->getRequest()->getParam('type'));
			$this->view->loginForm = $loginForm;

			$createForm = new Elm_Model_Form_User_Create();
			$createForm->setAction('/user/create-ajax?type=' . $this->getRequest()->getParam('type'));
			$createForm->removeElement('location');
			$this->view->createForm = $createForm;
		}

		$this->getResponse()->sendResponse();
	}

	/**
	 * Ajax step 3 for when plot is a garden
	 */
	public function gardenDetailsAction()
	{
		$this->_initAjax();
		$form = new Elm_Model_Form_Plot_Create();
		$form->setAction('/plot/create-ajax?type=' . $this->getRequest()->getParam('type'));
		$form->removeElement('submit');
		$this->view->form = $form;
	}
}

