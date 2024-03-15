<?php

declare(strict_types=1);

namespace App\Core\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use App\Core\Contracts\User\OwnableInterface;

class UserFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if ($targetEntity->getReflectionClass() !== null
            && !$targetEntity->getReflectionClass()->implementsInterface(OwnableInterface::class)) {
            return '';
        }

        return $targetTableAlias.'.user_id = '.$this->getParameter('user_id');
    }
}
