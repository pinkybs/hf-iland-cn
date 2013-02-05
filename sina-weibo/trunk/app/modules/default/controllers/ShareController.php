<?php

class ShareController extends Zend_Controller_Action
{
    public function init()
    {
    	$this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->staticUrl = STATIC_HOST;
        $this->view->hostUrl = HOST;
    }

    public function pengyouAction()
    {
        $user = $this->_request->getparam('user', 0);
        $this->view->user = $user;
    	$this->render();
    }

 }
