<?php

declare(strict_types=1);

namespace App\Domain\User\Controller;

use App\Application\Common\Controller\AbstractApplicationController;
use App\Application\Common\Exception\EntityValidationException;
use App\Application\Common\Exception\InvalidArgumentException;
use App\Application\Common\Manager\BaseManagerInterface;
use App\Domain\User\Builder\UserBaseBuilderInterface;
use App\Domain\User\Dto\UserRegistrationInputDto;
use App\Domain\User\Entity\User;
use App\Domain\User\Enum\UserSerializerGroupsEnum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractApplicationController
{
    /**
     * @throws EntityValidationException
     * @throws \JsonException
     * @throws InvalidArgumentException
     */
    #[Route(
        path: '/register',
        name: 'register',
        methods: [Request::METHOD_POST]
    )]
    public function register(
        Request $request,
        UserBaseBuilderInterface $builder,
        BaseManagerInterface $baseManager,
    ): Response {
        $requestInput = $request->getContent();

        /** @phpstan-ignore-next-line  */
        if (null === $requestInput) {
            throw new InvalidArgumentException('Invalid input');
        }

        /** @var string $requestInput */
        $input = $this->deserialize($requestInput);

        /** @var UserRegistrationInputDto $userRegistrationInput */
        $userRegistrationInput = $builder->build(UserRegistrationInputDto::class, $input);

        $user = $builder->buildForRegistration($userRegistrationInput);

        $baseManager->insert($user);

        return $this->output($user, groups: UserSerializerGroupsEnum::toArray());
    }

    /**
     * @throws \JsonException
     */
    #[Route(
        path: '/user/me',
        name: 'user_me_get',
        methods: [Request::METHOD_GET]
    )]
    public function me(): Response
    {
        /** @phpstan-ignore-next-line */
        return $this->output($this->getUser(), groups: UserSerializerGroupsEnum::toArray());
    }

    #[Route(
        path: '/user/{uuid}',
        name: 'user_get',
        methods: [Request::METHOD_GET]
    )]
    public function user(
        User $user,
    ): Response {
        return $this->output($user, groups: UserSerializerGroupsEnum::toArray());
    }
}
