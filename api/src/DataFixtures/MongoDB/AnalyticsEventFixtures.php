<?php
namespace App\DataFixtures\MongoDB;

use App\Document\AnalyticsEvent;
use Doctrine\ODM\MongoDB\DocumentManager as DocumentManager;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Faker\Factory;

class AnalyticsEventFixtures extends Fixture
{
    public function load(DocumentManager|\Doctrine\Persistence\ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $urls = [
            '/',
            '/login',
            '/register',
            '/product',
            '/quotation',
            '/dashboard',
            '/dashboard/users',
            '/dashboard/products',
            '/dashboard/warehouses',
            '/dashboard/quotations',
            '/dashboard/companies',
            '/quotation/detail/' . $faker->numberBetween(1, 100),
            '/order-history',
            '/profile',
        ];
        $eventTypes = ['page_view', 'click', 'scroll', 'download'];
        $deviceTypes = ['Mobile', 'Desktop', 'Tablet'];

        for ($i = 0; $i < 500; $i++) {
            $event = new AnalyticsEvent();
            $event->setUrl($urls[array_rand($urls)]);
            $event->setTimestamp($faker->dateTimeBetween('-30 days', 'now'));
            $event->setUserAgent($faker->userAgent);
            $event->setReferrer($faker->url);
            $event->setSessionDuration($faker->numberBetween(1000, 600000));
            $event->setDeviceType($deviceTypes[array_rand($deviceTypes)]);
            $event->setCountry($faker->countryCode);
            $event->setScreenWidth($faker->numberBetween(320, 1920));
            $event->setScreenHeight($faker->numberBetween(480, 1080));
            $event->setLanguage($faker->languageCode);
            $event->setIsBounce($faker->boolean(30));
            $event->setEventType($eventTypes[array_rand($eventTypes)]);
            $event->setPageTitle($faker->sentence(3));
            $event->setLoadTime($faker->numberBetween(100, 5000));
            $manager->persist($event);
        }
        $manager->flush();
    }
}
