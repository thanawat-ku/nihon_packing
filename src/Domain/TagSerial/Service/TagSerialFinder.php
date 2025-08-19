<?php

namespace App\Domain\TagSerial\Service;

use App\Domain\TagSerial\Repository\TagSerialRepository;

/**
 * Service.
 */
final class TagSerialFinder
{
    private $repository;

    public function __construct(TagSerialRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find customers.
     *
     * @param array<mixed> $params The parameters
     *
     * @return array<mixed> The result
     */
    public function findTagSerials(array $params): array
    {
        return $this->repository->findTagSerials($params);
    }
    public function getTagSerialNo($cusomerId): string
    {
        $ym = date("ym");
        $rt = $this->repository->getMaxNo($cusomerId, $ym);
        if ($rt) {
            $serial_no = $rt[0][0] + 1;
        } else {
            $serial_no = $ym . "00001";
        }
        return 1;
    }
}
