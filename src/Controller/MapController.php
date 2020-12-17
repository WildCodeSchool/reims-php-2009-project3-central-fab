<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FablabRepository;
use App\Entity\Fablab;
use maxh\Nominatim\Nominatim;

/**
 * @Route("/map")
*/
class MapController extends AbstractController
{
    /**
     * @Route("/", name="map")
     */
    public function index(): Response
    {
        $fablabRepository = $this->getDoctrine()
             ->getRepository(Fablab::class)
             ->findAll();

        if (!$fablabRepository) {
            throw $this->createNotFoundException(
                'No Fablab found in Falab\'s table.'
            );
        }

        $url = 'http://nominatim.openstreetmap.org/';
        $nominatim = new Nominatim($url);

        $data = [];
        foreach ($fablabRepository as $address) {
            $search = $nominatim->newSearch();
            $mapAddress = $search->query('' . $address->getAdress() . '');
            $data[] = $nominatim->find($mapAddress);
        }

        return $this->render('map/index.html.twig', [
            'cities' => $fablabRepository,
            'maps' => $data,
        ]);
    }
}
