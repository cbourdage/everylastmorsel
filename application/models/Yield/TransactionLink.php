<?php

class Elm_Model_Yield_TransactionLink extends Colony_Model_Abstract
{
	public function _construct()
    {
        $this->_init('yield_transactionLink');
    }

	public function loadByTransactionId($id)
	{
		return $this->_getResource()->loadByTransactionId($id);
	}

	/**
	 * Returns the Elm_Model_User if the seller id exists
	 *
	 * @return mixed
	 */
	public function getSeller()
	{
		if (!$this->getData('seller') && $this->getSellerId()) {
			$this->setData('seller', Elm::getModel('user')->load($this->getSellerId()));
		}
		return $this->getData('seller');
	}

	/**
	 * Returns the Elm_Model_User if the buyer id exists
	 *
	 * @return mixed
	 */
	public function getBuyer()
	{
		if (!$this->getData('buyer') && $this->getBuyerId()) {
			$this->setData('buyer', Elm::getModel('user')->load($this->getBuyerId()));
		}
		return $this->getData('buyer');
	}

	/**
	 * @param Elm_Model_Yield_Transaction $trans
	 * @return Elm_Model_Yield_TransactionLink
	 */
	public function linkThemUp(Elm_Model_Yield_Transaction $trans)
	{
		$this->setTransaction($trans);
		$this->setBuyerId(Elm::getSingleton('user/session')->getUser()->getId());
		$this->setSellerId($trans->getPurchasableObject()->getPlotCrop()->getUserId());
		$this->setTransactionId($trans->getId());
		$this->save();

		$this->sendCommunicationMessage($trans);
		return $this;
	}

	/**
	 * Sends the email notifying a seller of a new purchase request
	 * and adds the message to the users inbox.
	 *
	 * @param Elm_Model_Yield_Transaction $trans
	 * @return bool
	 */
	public function sendCommunicationMessage(Elm_Model_Yield_Transaction $trans)
	{
		$purchasableObj = $trans->getPurchasableObject();
		$comm = Elm::getModel('communication')->init(array(
			'to_user_id' => $this->getSeller()->getId(),
			'from_user_id' => $this->getBuyer()->getId(),
			'subject' => Elm_Model_Yield_Transaction::REASON_PURCHASE,
			'message' => sprintf(
				"%s would like to purchase %s totaling $%s from you. \n\n Reply here to facilitate the transaction.",
				$this->getBuyer()->getName(),
				$trans->getQuantity() . ' ' . $trans->getQuantityUnit(),
				$trans->getTotal()
			)
		));

		try {
			$EmailTemplate = new Elm_Model_Email_Template(array('template' => 'purchase-request.phtml'));
			$EmailTemplate->setParams(array(
				'subject' => Elm_Model_Yield_Transaction::REASON_PURCHASE,
				'buyer' => $this->getBuyer(),
				'seller' => $this->getSeller(),
				'transaction' => $trans,
			));
			$EmailTemplate->setFromName($this->getBuyer()->getName());
			$EmailTemplate->send(array('email' => $this->getSeller()->getEmail(), 'name' => $this->getSeller()->getName()));

			// Save message
			$comm->setDelivered(true)->save();
			return true;
		} catch(Exception $e) {
			$comm->setDelivered(false)->save();
			Elm::logException($e);
			return false;
		}

		return $this;
	}
}
