<?php 

namespace App\Entity;

class Recherche
{
    // creation d'une propriété qui prendra tout les champs de user en compte
    private $q;

    /**
     * Get the value of q
     */
    public function getQ(): ?string
    {
        return $this->q;
    }

    /**
     * Set the value of q
     *
     * @return  self
     */
    public function setQ(?string $q): self
    {
        $this->q = $q;

        return $this;
    }

    
}