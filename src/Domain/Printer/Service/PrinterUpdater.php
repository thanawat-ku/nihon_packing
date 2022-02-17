<?php

namespace App\Domain\Printer\Service;

use App\Domain\Printer\Repository\PrinterRepository;

/**
 * Service.
 */
final class PrinterUpdater
{
    private $repository;
    private $validator;

    public function __construct(
        PrinterRepository $repository,
        PrinterValidator $validator
    ) {
        $this->repository = $repository;
        $this->validator = $validator;
        //$this->logger = $loggerFactory
        //->addFileHandler('store_updater.log')
        //->createInstance();
    }

    public function insertPrinter(array $data): int
    {
        $this->validator->validatePrinterInsert($data);

        $row = $this->mapToPrinterRow($data);

        $id = $this->repository->insertPrinter($row);

        return $id;
    }
    public function updatePrinter(int $printerID, array $data): void
    {
        $this->validator->validatePrinterUpdate($printerID, $data);

        $row = $this->mapToPrinterRow($data);

        $this->repository->updatePrinter($printerID, $row);
    }

    public function deletePrinter(int $printerId): void
    {
        $this->repository->deletePrinter($printerId);
    }

    /**
     * Map data to row.
     *
     * @param array<mixed> $data The data
     *
     * @return array<mixed> The row
     */
    private function mapToPrinterRow(array $data): array
    {
        $result = [];

        if (isset($data['printer_name'])) {
            $result['printer_name'] = (string)$data['printer_name'];
        }
        if (isset($data['printer_address'])) {
            $result['printer_address'] = (string)$data['printer_address'];
        }
        if (isset($data['printer_type'])) {
            $result['printer_type'] = (string)$data['printer_type'];
        }

        return $result;
    }
}
