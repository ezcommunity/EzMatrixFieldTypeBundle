<?php

namespace EzSystems\MatrixBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction( $name )
    {
        return $this->render('EzMatrixBundle:Default:index.html.twig', array('name' => $name));
    }
}
