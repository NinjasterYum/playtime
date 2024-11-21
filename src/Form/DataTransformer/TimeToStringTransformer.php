<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimeToStringTransformer implements DataTransformerInterface
{
    public function transform(mixed $time): mixed
    {
        if (null === $time) {
            return '';
        }

        if ($time instanceof \DateTimeInterface) {
            return $time->format('H:i');
        }

        return $time;
    }

    public function reverseTransform(mixed $timeString): mixed
    {
        if (!$timeString) {
            return null;
        }

        if (!is_string($timeString)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $datetime = \DateTime::createFromFormat('H:i', $timeString);

        if (!$datetime) {
            throw new TransformationFailedException('Invalid time format');
        }

        return $datetime;
    }
}