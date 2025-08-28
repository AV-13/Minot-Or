<?php
namespace App\Controller;

use App\Document\AnalyticsEvent;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AnalyticsController extends AbstractController
{
    #[Route('/api/analytics', name: 'analytics_create', methods: ['POST'])]
    public function create(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (empty($data['url'])) {
            return $this->json(['error' => 'Missing url'], 400);
        }

        $event = new AnalyticsEvent();
        $event->setUrl($data['url']);
        $event->setTimestamp(new \DateTime($data['timestamp'] ?? 'now'));
        $event->setUserAgent($data['userAgent'] ?? null);
        $event->setReferrer($data['referrer'] ?? null);
        $event->setDeviceType($data['deviceType'] ?? null);
        $event->setScreenWidth($data['screenWidth'] ?? null);
        $event->setScreenHeight($data['screenHeight'] ?? null);
        $event->setLanguage($data['language'] ?? null);
        $event->setEventType($data['eventType'] ?? null);
        $event->setPageTitle($data['pageTitle'] ?? null);
        $event->setLoadTime($data['loadTime'] ?? null);

        $dm->persist($event);
        $dm->flush();

        return $this->json([
            'message' => 'Event recorded',
            'data' => [
                'id' => $event->getId(),
                'url' => $event->getUrl(),
                'timestamp' => $event->getTimestamp()?->format('Y-m-d H:i:s'),
                'userAgent' => $event->getUserAgent(),
                'referrer' => $event->getReferrer(),
                'deviceType' => $event->getDeviceType(),
                'screenWidth' => $event->getScreenWidth(),
                'screenHeight' => $event->getScreenHeight(),
                'language' => $event->getLanguage(),
                'eventType' => $event->getEventType(),
                'pageTitle' => $event->getPageTitle(),
                'loadTime' => $event->getLoadTime(),
            ]
        ], 201);
    }

    #[Route('/api/analytics', name: 'analytics_list', methods: ['GET'])]
    public function list(DocumentManager $dm): JsonResponse
    {
        $events = $dm->getRepository(AnalyticsEvent::class)->findAll();
        $data = array_map(fn($e) => [
            'id' => $e->getId(),
            'url' => $e->getUrl(),
            'timestamp' => $e->getTimestamp()?->format('Y-m-d H:i:s'),
            'userAgent' => $e->getUserAgent(),
            'referrer' => $e->getReferrer(),
            'sessionDuration' => $e->getSessionDuration(),
            'deviceType' => $e->getDeviceType(),
            'country' => $e->getCountry(),
            'screenWidth' => $e->getScreenWidth(),
            'screenHeight' => $e->getScreenHeight(),
            'language' => $e->getLanguage(),
            'isBounce' => $e->getIsBounce(),
            'eventType' => $e->getEventType(),
            'pageTitle' => $e->getPageTitle(),
            'loadTime' => $e->getLoadTime(),
        ], $events);

        return $this->json($data);
    }
    #[Route('/api/analytics/{id}', name: 'analytics_delete', methods: ['DELETE'])]
    public function delete(string $id, DocumentManager $dm): JsonResponse
    {
        $event = $dm->getRepository(AnalyticsEvent::class)->find($id);
        if (!$event) {
            return $this->json(['error' => 'Not found'], 404);
        }
        $dm->remove($event);
        $dm->flush();
        return $this->json(['message' => 'Event deleted']);
    }
}
