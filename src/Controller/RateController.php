<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Currency;
use App\Entity\Provider;

class RateController extends AbstractController
{
    /**
     * Код Центрального Банка Российской Федерации
     */
    const CBR = 'RussianCentralBank';

    /**
     * Код Центрального Европейского Банка
     */
    const ECB = 'EuropeanCentralBank';

    /**
     * конструктор данных источников
     */
    private function __construct()
    {

    }

    /**
     * @Route("/rate/euro")
     */
    public function getEuro()
    {
        $euro = new Currency(Currency::EUR);
        $rub = new Currency(Currency::RUB);
        $cmr = Provider::getProvider(RateController::CBR);
        $ecb = Provider::getProvider('EuropeanCentralBank');
        $cmr->setPriority(0);
        $ecb->setPriority(1);

        foreach (Provider::getSortedProviders() as $key => $item) {
            $source = Provider::getProvider($key);
            $sourceName = $item["title"];
        }

        $euro = $rub->getRate($source, $euro);

        return $this->render( '/rate/euro.html.twig', [
            'euro' => $euro, 'sourceName' => $sourceName,
        ]);
    }

    /**
     * @Route("/rate/euro_ajax", name="app_rate_euro_ajax", methods={"POST"})
     */
    public function getEuroAjax(Request $request):Response
    {
        $euro = new Currency(Currency::EUR);
        $rub = new Currency(Currency::RUB);
        $cmr = Provider::getProvider('RussianCentralBank');
        $ecb = Provider::getProvider('EuropeanCentralBank');
        $cmr->setPriority(2);
        $ecb->setPriority(1);

        foreach (Provider::getSortedProviders() as $key => $item) {
            $source = Provider::getProvider($key);
            $sourceName = $item["title"];
        }

        $euro = $rub->getRate($source, $euro);

        return $this->render('/rate/euro_ajax.html.twig', [
            'euro' => $euro, 'sourceName' => $sourceName,
        ]);
    }
}