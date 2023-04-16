<?php

namespace App\Dto;

use Nelexa\RequestDtoBundle\Dto\RequestObjectInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class  WebExtensionRequest implements RequestObjectInterface
{
    #[NotBlank()]
    #[Url()]
    public ?string $url;

    #[NotBlank()]
    #[Length(min: 5)]
    public ?string $body;

    #[NotBlank()]
    public ?string $title;
}
