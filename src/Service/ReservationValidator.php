<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Psr\Log\LoggerInterface;

class ReservationValidator
{
    private $logger;
    private $reservationRepository;

    public function __construct(LoggerInterface $logger, ReservationRepository $reservationRepository)
    {
        $this->logger = $logger;
        $this->reservationRepository = $reservationRepository;
    }
    public function validate(Reservation $reservation): ValidationResult
    {
        $result = new ValidationResult();
        
        $company = $reservation->getSportCompany();
        $date = $reservation->getDate();
        $time = $reservation->getTime();
        
        if (!$date instanceof \DateTime || !$time instanceof \DateTime) {
            $result->addError('La date ou l\'heure de réservation n\'est pas valide.');
            return $result;
        }
    
        $dayOfWeek = strtolower($date->format('l'));
        $timeString = $time->format('H:i');
    
    $dayTranslations = [
        'monday' => 'lundi',
        'tuesday' => 'mardi',
        'wednesday' => 'mercredi',
        'thursday' => 'jeudi',
        'friday' => 'vendredi',
        'saturday' => 'samedi',
        'sunday' => 'dimanche'
    ];
    
    $frenchDayOfWeek = $dayTranslations[$dayOfWeek] ?? $dayOfWeek;
    
    $schedule = $company->getSchedules()->filter(function($s) use ($frenchDayOfWeek) {
        return strtolower($s->getDayOfWeek()) === $frenchDayOfWeek;
    })->first();

    if (!$schedule) {
        $result->addError('L\'entreprise est fermée ce jour-là.');
    } else {
        $openingTime = $schedule->getOpeningTime()->format('H:i');
        $closingTime = $schedule->getClosingTime()->format('H:i');
        
        if ($timeString < $openingTime || $timeString > $closingTime) {
            $result->addError('L\'heure de réservation est en dehors des horaires d\'ouverture.');
        }
    }

    $existingReservation = $this->reservationRepository->findOneBy([
        'sportCompany' => $company,
        'date' => $date,
        'time' => $time,
    ]);

    if ($existingReservation) {
        $result->addError('Ce créneau horaire est déjà réservé.');
    }
    return $result;
}
}
class ValidationResult
{
    private $errors = [];
    
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
    
    public function getErrors(): array
    {
        return $this->errors;
    }
    
    public function isValid(): bool
    {
        return empty($this->errors);
    }
}