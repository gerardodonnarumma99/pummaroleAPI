<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timers
 *
 * @ORM\Table(name="timers", indexes={@ORM\Index(name="timer_type", columns={"timer_type"}), @ORM\Index(name="user_id", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\TimersRepository")
 */
class Timers
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=false)
     */
    private $startDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=0, nullable=false)
     */
    private $status;

    /**
     * @var \TimerType
     *
     * @ORM\OneToOne(targetEntity="TimerType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="timer_type", referencedColumnName="id")
     * })
     */
    private $timerType;

    /**
     * @var \User
     *
     * @ORM\Column(type="string", name="user_id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $firstCycle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTimerType(): ?TimerType
    {
        return $this->timerType;
    }

    public function setTimerType(?TimerType $timerType): self
    {
        $this->timerType = $timerType;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFirstCycle(): ?string
    {
        return $this->firstCycle;
    }

    public function setFirstCycle(string $firstCycle): self
    {
        $this->firstCycle = $firstCycle;

        return $this;
    }


}
