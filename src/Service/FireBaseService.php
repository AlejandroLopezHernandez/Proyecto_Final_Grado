<?php

namespace App\Service;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Firestore;

class FireBaseService
{
    private Firestore $firestore;

    public function __construct(string $credentialsPath)
    {
        $factory = (new Factory)->withServiceAccount($credentialsPath);
        $this->firestore = $factory->createFirestore();
    }

    public function getFirestore(): Firestore
    {
        return $this->firestore;
    }
}
