<?php
// includes/OrderProcessor.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

class OrderProcessor {
    private $pdoCart;
    private $partnerId;
    
    public function __construct() {
        // Ensure user is authenticated
        require_auth();
        
        $this->pdoCart = getCartDbConnection();
        $this->partnerId = $_SESSION['auth']['partner_id'];
    }
    
    /**
     * Process order submission
     */
    public function processOrder($orderData) {
        try {
            // Validate input
            $this->validateOrderData($orderData);
            
            // Generate unique order number
            $orderNumber = $this->generateOrderNumber();
            
            // Calculate totals
            $totalAmount = $this->calculateTotalAmount($orderData['items']);
            
            // Begin transaction
            $this->pdoCart->beginTransaction();
            
            try {
                // Insert order
                $orderId = $this->insertOrder($orderNumber, $totalAmount);
                
                // Insert cart items
                $this->insertCartItems($orderId, $orderData['items']);
                
                // Commit transaction
                $this->pdoCart->commit();
                
                return [
                    'success' => true,
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                    'total_amount' => $totalAmount,
                    'message' => 'Order placed successfully!'
                ];
                
            } catch (Exception $e) {
                // Rollback on error
                $this->pdoCart->rollBack();
                error_log("Order processing error: " . $e->getMessage());
                throw new Exception('Failed to process order. Please try again.');
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Validate order data
     */
    private function validateOrderData($orderData) {
        if (empty($orderData['items']) || !is_array($orderData['items'])) {
            throw new Exception('Invalid order data: items array required');
        }
        
        if (count($orderData['items']) === 0) {
            throw new Exception('Order must contain at least one item');
        }
        
        foreach ($orderData['items'] as $item) {
            if (empty($item['product_name']) || strlen($item['product_name']) > 512) {
                throw new Exception('Invalid product name');
            }
            
            if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] < 1 || $item['quantity'] > 1000) {
                throw new Exception('Invalid quantity: must be between 1 and 1000');
            }
            
            if (!isset($item['unit_price']) || !is_numeric($item['unit_price']) || $item['unit_price'] < 0) {
                throw new Exception('Invalid unit price: must be non-negative');
            }
        }
    }
    
    /**
     * Generate unique order number
     */
    private function generateOrderNumber() {
        $prefix = 'ORD';
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        return $prefix . $timestamp . $random;
    }
    
    /**
     * Calculate total amount
     */
    private function calculateTotalAmount($items) {
        $total = 0;
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $total += $subtotal;
        }
        return round($total, 2);
    }
    
    /**
     * Insert order record
     */
    private function insertOrder($orderNumber, $totalAmount) {
        $stmt = $this->pdoCart->prepare("
            INSERT INTO orders (partner_id, order_number, total_amount, currency, status)
            VALUES (:partner_id, :order_number, :total_amount, :currency, 'pending')
        ");
        
        $stmt->execute([
            ':partner_id' => $this->partnerId,
            ':order_number' => $orderNumber,
            ':total_amount' => $totalAmount,
            ':currency' => 'MAD'
        ]);
        
        return $this->pdoCart->lastInsertId();
    }
    
    /**
     * Insert cart items
     */
    private function insertCartItems($orderId, $items) {
        $stmt = $this->pdoCart->prepare("
            INSERT INTO cart_items (order_id, product_name, product_sku, quantity, unit_price, subtotal, status)
            VALUES (:order_id, :product_name, :product_sku, :quantity, :unit_price, :subtotal, 'new')
        ");
        
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $stmt->execute([
                ':order_id' => $orderId,
                ':product_name' => $item['product_name'],
                ':product_sku' => $item['product_sku'] ?? null,
                ':quantity' => $item['quantity'],
                ':unit_price' => $item['unit_price'],
                ':subtotal' => $subtotal
            ]);
        }
    }
    
    /**
     * Get order history for current partner
     */
    public function getOrderHistory() {
        $stmt = $this->pdoCart->prepare("
            SELECT o.*, COUNT(ci.id) as item_count
            FROM orders o
            LEFT JOIN cart_items ci ON o.id = ci.order_id
            WHERE o.partner_id = :partner_id
            GROUP BY o.id, o.order_number, o.status, o.total_amount, o.currency, o.created_at, o.updated_at
            ORDER BY o.created_at DESC
        ");
        
        $stmt->execute([':partner_id' => $this->partnerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get order details
     */
    public function getOrderDetails($orderId) {
        // Verify order belongs to current partner
        $stmt = $this->pdoCart->prepare("
            SELECT * FROM orders 
            WHERE id = :order_id AND partner_id = :partner_id
        ");
        
        $stmt->execute([
            ':order_id' => $orderId,
            ':partner_id' => $this->partnerId
        ]);
        
        $order = $stmt->fetch();
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        return $order;
    }

    /**
     * Get order items for a specific order
     */
    public function getOrderItems($orderId) {
        try {
            $stmt = $this->pdoCart->prepare("
                SELECT * FROM cart_items 
                WHERE order_id = ?
                ORDER BY id ASC
            ");
            $stmt->execute([$orderId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Failed to get order items: " . $e->getMessage());
            throw new Exception('Failed to retrieve order items');
        }
    }
}
?>
