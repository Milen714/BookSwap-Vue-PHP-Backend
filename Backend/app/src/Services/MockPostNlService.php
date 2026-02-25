<?php 
namespace App\Services;
use App\Models\Book;

class MockPostNlService {
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
    private function getRandomGSM(): int {
        return rand(70, 100);
    }
    private function getRandomWidth(): float {
        return (float)rand(108 , 1524) / 100;
    }
    private function getRandomHeight(): float {
        return (float)rand(175, 2286) / 100;
    }
    private function getRandomPageCount(): int {
        return rand(250 , 350);
    }
    private function calculateaBookWeight(int $pageCount): float {
        if ($pageCount <= 0) {
            $pageCount = $this->getRandomPageCount();
        }
        $weight = $pageCount * $this->getRandomGSM() * $this->getRandomWidth() * $this->getRandomHeight() / 1000000;   
        return $weight;
    }

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