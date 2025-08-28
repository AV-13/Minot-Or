<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class AnalyticsEvent
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: 'string')]
    private $url;

    #[MongoDB\Field(type: 'date')]
    private $timestamp;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private $userAgent;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private $referrer;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private $deviceType;

    #[MongoDB\Field(type: 'int', nullable: true)]
    private $screenWidth;

    #[MongoDB\Field(type: 'int', nullable: true)]
    private $screenHeight;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private $language;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private $eventType;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private $pageTitle;

    #[MongoDB\Field(type: 'int', nullable: true)]
    private $loadTime;

    // Getters/setters à ajouter
    // id
    public function getId(): ?string
    {
        return $this->id;
    }

// url
    public function getUrl(): ?string
    {
        return $this->url;
    }
    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

// timestamp
    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }
    public function setTimestamp(?\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

// userAgent
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }
    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

// referrer
    public function getReferrer(): ?string
    {
        return $this->referrer;
    }
    public function setReferrer(?string $referrer): self
    {
        $this->referrer = $referrer;
        return $this;
    }

// deviceType
    public function getDeviceType(): ?string
    {
        return $this->deviceType;
    }
    public function setDeviceType(?string $deviceType): self
    {
        $this->deviceType = $deviceType;
        return $this;
    }

// screenWidth
    public function getScreenWidth(): ?int
    {
        return $this->screenWidth;
    }
    public function setScreenWidth(?int $screenWidth): self
    {
        $this->screenWidth = $screenWidth;
        return $this;
    }

// screenHeight
    public function getScreenHeight(): ?int
    {
        return $this->screenHeight;
    }
    public function setScreenHeight(?int $screenHeight): self
    {
        $this->screenHeight = $screenHeight;
        return $this;
    }

// language
    public function getLanguage(): ?string
    {
        return $this->language;
    }
    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }


// eventType
    public function getEventType(): ?string
    {
        return $this->eventType;
    }
    public function setEventType(?string $eventType): self
    {
        $this->eventType = $eventType;
        return $this;
    }

// pageTitle
    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }
    public function setPageTitle(?string $pageTitle): self
    {
        $this->pageTitle = $pageTitle;
        return $this;
    }

// loadTime
    public function getLoadTime(): ?int
    {
        return $this->loadTime;
    }
    public function setLoadTime(?int $loadTime): self
    {
        $this->loadTime = $loadTime;
        return $this;
    }
}
