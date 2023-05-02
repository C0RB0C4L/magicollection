<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Timestamped by default.
 */
trait UserBaseTrait
{
	#[ORM\Column]
	private ?bool $isActive = false;

	#[ORM\Column]
	private ?bool $isVerified = false;

	#[ORM\Column(nullable: true)]
	private ?\DateTimeImmutable $registeredAt = null;

	#[ORM\Column(nullable: true)]
	private ?\DateTimeImmutable $lastLoginAt = null;

	public function isActive(): ?bool
	{
		return $this->isActive;
	}

	public function setIsActive(bool $isActive): self
	{
		$this->isActive = $isActive;

		return $this;
	}

	public function isVerified(): ?bool
	{
		return $this->isVerified;
	}

	public function setIsVerified(bool $isVerified): self
	{
		$this->isVerified = $isVerified;

		return $this;
	}

	public function getRegisteredAt(): ?\DateTimeImmutable
	{
		return $this->registeredAt;
	}

	public function setRegisteredAt(\DateTimeImmutable $registeredAt): self
	{
		$this->registeredAt = $registeredAt;

		return $this;
	}

	public function getLastLoginAt(): ?\DateTimeImmutable
	{
		return $this->lastLoginAt;
	}

	public function setLastLoginAt(?\DateTimeImmutable $lastLoginAt): self
	{
		$this->lastLoginAt = $lastLoginAt;

		return $this;
	}
}
