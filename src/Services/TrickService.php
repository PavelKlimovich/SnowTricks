<?php

namespace App\Services;

use App\Repository\TrickRepository;

class TrickService
{
    private TrickRepository $trickRepository; 

    public function __construct(TrickRepository $trickRepository) {
        $this->trickRepository = $trickRepository;
    }

    public function getTricks()
    {
        return $this->trickRepository->findAll();
    }
}