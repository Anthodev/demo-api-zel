<?php

declare(strict_types=1);

namespace App\Application\Common\Controller;

use const JSON_THROW_ON_ERROR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

abstract class AbstractApplicationController extends AbstractController
{
    /**
     * @return array<string, mixed>
     * @throws \JsonException
     */
    public function deserialize(string $data): array
    {
        /** @var array<string, mixed> */
        return \json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, mixed> $context
     */
    public function output(object $data, array $context = [], bool $forceObject = false): JsonResponse
    {
        if (count($context) === 0) {
            $context = $this->defaultOutputContext();
        }

        if ($forceObject) {
            $dataJson = \json_encode($data, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT);
            return $this->json($dataJson, context: $context);
        }

        return $this->json($data, context: $context);
    }

    /**
     * @param list<string> $groups
     */
    public function setObjectContext(array $groups): ObjectNormalizerContextBuilder
    {
        return (new ObjectNormalizerContextBuilder())
            ->withGroups($groups)
        ;
    }

    /**
     * @return list<ObjectNormalizerContextBuilder>
     */
    private function defaultOutputContext(): array
    {
        return [
            (new ObjectNormalizerContextBuilder())
                ->withGroups(['read'])
        ];
    }
}
