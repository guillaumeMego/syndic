<?php 

namespace App\Entity;

class Recherche
{

    private $q;

    public function getQ(): ?string
    {
        return $this->q;
    }

    public function setQ(?string $q): self
    {
        $this->q = $q;

        return $this;
    }
}