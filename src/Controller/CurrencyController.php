<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Currency;
use App\Entity\Provider;

class CurrencyController extends AbstractController
{
    /**
     * @Route("/currency/euro")
     */
    public function euro()
    {
        $euro = new Currency(Currency::EUR);
        $rub = new Currency(Currency::RUB);
        $cmr = Provider::getProvider('RussianCentralBank');
        $cmr->setPriority(1);
        // print_r($cmr);
        // exit;

        foreach (array_keys(Provider::getProviders()) as $key => $item) {
            if($key == 0){
                continue;
            }
            $source = Provider::getProvider($item);
        }

        $euro = $rub->getRate($source, $euro);

        return $this->render( '/currency/euro.html.twig', [
            'euro' => $euro,
        ]);
    }
}