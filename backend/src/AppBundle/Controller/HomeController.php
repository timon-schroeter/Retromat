<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", defaults={"_locale": "en"}, name="home_slash")
     * @Route("/index.html", defaults={"_locale": "en"}, name="home_default")
     * @Route("/index_{_locale}.html", requirements={"_locale": "en|de|fr|es|nl"}, name="home")
     */
    public function homeAction(Request $request)
    {
        if (empty($request->query->get('id'))) {
            // Original retromat redirects from / to a random plan. Implementing static redirection to get started.
            // Start with beginners plan for now: http://finding-marbles.com/retr-o-mat/the-best-retrospective-for-beginners/
            return $this->redirect('/?id=122-9-51-39-60', $status = 302);
        }

        $ids = explode('-', $request->query->get('id'));
        $phase = $request->query->get('phase');

        $activities = $this->get('doctrine.orm.entity_manager')->getRepository('AppBundle:Activity')->findOrdered(
            $request->getLocale(),
            $ids
        );

        return $this->render('home/index_'.$request->getLocale().'.html.twig', ['activities' => $activities, 'phase' => $phase]);
    }
}