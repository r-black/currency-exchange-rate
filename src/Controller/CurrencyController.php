<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Currency;
use App\Entity\Provider;

class CurrencyController extends AbstractController
{

    /**
     * @Route("/rate/euro", name="app_rate_euro")
     */
    public function indexAction()
    {
        $euro = new Currency(Currency::EUR);
        $rub = new Currency(Currency::RUB);
        $cbr = Provider::getProvider('RussianCentralBank');
        $ecb = Provider::getProvider('EuropeanCentralBank');
        $cbr->setPriority(0);
        $ecb->setPriority(1);

        foreach (Provider::getSortedProviders() as $key => $item) {
            $source = Provider::getProvider($key);
            $sourceName = $item["title"];
        }

        $euroRate = $rub->getRate($source, $euro);

        return $this->render( '/rate/euro.html.twig', [
            'euro' => $euroRate, 'sourceName' => $sourceName,
        ]);
    }

    

    /**
     * @Route("/rate/euro_ajax", methods={"POST"}, name="app_rate_euro_ajax")
     */
    public function getEuroAjax()
    {
        $euro = new Currency(Currency::EUR);
        $rub = new Currency(Currency::RUB);
        $cbr = Provider::getProvider('RussianCentralBank');
        $ecb = Provider::getProvider('EuropeanCentralBank');
        $cbr->setPriority(2);
        $ecb->setPriority(1);

        foreach (Provider::getSortedProviders() as $key => $item) {
            $source = Provider::getProvider($key);
            $sourceName = $item["title"];
        }

        $euroRate = $rub->getRate($source, $euro);

        return $this->render('/rate/euro_ajax.html.twig', [
            'euro' => $euroRate, 'sourceName' => $sourceName,
        ]);
    }
}