<?php 
namespace App\Services;
use App\Models\Book;

/**
 * Mock shipping provider used for local development and testing.
 */
class MockPostNlService {
    /**
     * Estimate shipping cost based on mocked calculated book weight.
     *
     * @param Book $book Book details.
     * @return float Shipping cost in EUR.
     */
    public function calculateShippingCost(Book $book): float {
        // Mock implementation: flat rate based on weight
        $weight = $this->calculateaBookWeight($book->page_count);
        if ($weight <= 1.0) {
            return 5.0; // Flat rate for up to 1kg
        } elseif ($weight <= 5.0) {
            return 8.0; // Flat rate for up to 5kg
        } else {
            return 20.0; // Flat rate for over 5kg
        }
    }

    /**
     * Generate random paper weight in GSM.
     *
     * @return int Paper weight in grams per square meter.
     */
    private function getRandomGSM(): int {
        return rand(70, 100);
    }

    /**
     * Generate random page width in centimeters.
     *
     * @return float Page width.
     */
    private function getRandomWidth(): float {
        return (float)rand(108 , 1524) / 100;
    }

    /**
     * Generate random page height in centimeters.
     *
     * @return float Page height.
     */
    private function getRandomHeight(): float {
        return (float)rand(175, 2286) / 100;
    }

    /**
     * Generate random page count used when input count is unavailable.
     *
     * @return int Page count.
     */
    private function getRandomPageCount(): int {
        return rand(250 , 350);
    }

    /**
     * Calculate approximate book weight based on dimensions and page count.
     *
     * @param int $pageCount Total pages.
     * @return float Estimated weight in kilograms.
     */
    private function calculateaBookWeight(int $pageCount): float {
        if ($pageCount <= 0) {
            $pageCount = $this->getRandomPageCount();
        }
        $weight = $pageCount * $this->getRandomGSM() * $this->getRandomWidth() * $this->getRandomHeight() / 1000000;   
        return $weight;
    }

    /**
     * Return mocked tracking data for a shipment.
     *
     * @param string $trackingNumber Carrier tracking number.
     * @return array{tracking_number: string, status: string, estimated_delivery: string, history: array<int, array{date: string, location: string, status: string}>}
     */
    public function trackShipment(string $trackingNumber): array {
        // Mock implementation: return dummy tracking info
        return [
            'tracking_number' => $trackingNumber,
            'status' => 'In Transit',
            'estimated_delivery' => date('Y-m-d', strtotime('+3 days')),
            'history' => [
                ['date' => date('Y-m-d', strtotime('-2 days')), 'location' => 'Origin Facility', 'status' => 'Shipment received'],
                ['date' => date('Y-m-d', strtotime('-1 days')), 'location' => 'Transit Hub', 'status' => 'In transit'],
            ],
        ];
    }
}