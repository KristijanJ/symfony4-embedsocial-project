<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LuckyController extends AbstractController
{
    public function number()
    {
        $number = random_int(0, 100);

        // return new Response(
        //     '<html><body>Lucky number: ' . $number . '</body></html>'
        // );

        $data = array();
        $data['number'] = $number;

        return $this->render('Lucky/number.html.twig', $data);
    }
}