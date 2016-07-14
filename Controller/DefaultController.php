<?php

namespace Erfans\AssetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ErfansAssetBundle:Default:index.html.twig');
    }
}
