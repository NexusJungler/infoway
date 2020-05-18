<?php

// src/Form/DataTransformer/IssueToNumberTransformer.php
namespace App\Form\DataTransformer;

use App\Entity\Customer\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaToNumberTransformer implements DataTransformerInterface
{
private $entityManager;

public function __construct(EntityManagerInterface $entityManager)
{
$this->entityManager = $entityManager;
}

/**
* Transforms an object (issue) to a string (number).
*
* @param  Media|null $issue
* @return string
*/
public function transform($media)
{
if (null === $media) {
return '';
}

return $media->getId();
}

/**
* Transforms a string (number) to an object (issue).
*
* @param  string $issueNumber
* @return Media|null
* @throws TransformationFailedException if object (issue) is not found.
*/
public function reverseTransform($mediaId)
{
// no issue number? It's optional, so that's ok
if (!$mediaId) {
return;
}

$media = $this->entityManager
->getRepository(Media::class)
// query for the issue with this id
->find($mediaId)
;

if (null === $media) {
// causes a validation error
// this message is not shown to the user
// see the invalid_message option
throw new TransformationFailedException(sprintf(
'An issue with number "%s" does not exist!',
$mediaId
));
}

return $media;
}
}