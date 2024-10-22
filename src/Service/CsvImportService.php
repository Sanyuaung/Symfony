<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CsvImportService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function importFromCsv($file): void
    {
        // Read and parse CSV file
        $data = array_map('str_getcsv', file($file->getRealPath()));

        // Iterate over each row in the CSV
        foreach ($data as $row) {
            // Skip header row if present
            if ($row[0] === 'Name') {
                continue; // Skip the header row
            }

            $product = new Product();

            // Set product fields from the CSV
            $product->setName($row[0]); // Name
            $product->setPrice(floatval($row[1])); // Price
            $product->setDescription($row[2]); // Description
            $product->setStockQuantity(intval($row[3])); // Stock Quantity

            // Parse the created date and time from dd/mm/yyyy hh:mm:ss a format
            $createdDateTimeString = $row[4] . ' ' . $row[5]; // Combine date and time
            $createdDateTime = \DateTime::createFromFormat('d/m/Y h:i:s a', $createdDateTimeString);

            if ($createdDateTime) {
                $product->setCreatedDatetime($createdDateTime);
            } else {
                // Handle the error case (invalid date format)
                throw new Exception("Invalid date format in CSV: " . $createdDateTimeString);
            }

            // Persist the product
            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();
    }
}
