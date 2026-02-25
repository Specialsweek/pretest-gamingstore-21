<?php
class Promotion
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function validateCode($code, $orderAmount)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM promotions WHERE promo_code = ? AND status = 'active'");
        $stmt->execute([$code]);
        $promo = $stmt->fetch();

        if (!$promo) {
            return ['valid' => false, 'message' => 'Invalid promo code.'];
        }

        // Check dates
        $now = date('Y-m-d H:i:s');
        if ($promo['start_date'] && $now < $promo['start_date']) {
            return ['valid' => false, 'message' => 'This promotion has not started yet.'];
        }
        if ($promo['end_date'] && $now > $promo['end_date']) {
            return ['valid' => false, 'message' => 'This promotion has expired.'];
        }

        // Check usage limit
        if ($promo['usage_limit'] !== null && $promo['used_count'] >= $promo['usage_limit']) {
            return ['valid' => false, 'message' => 'Promotion usage limit reached.'];
        }

        // Check minimum order amount
        if ($orderAmount < $promo['min_order_amount']) {
            return ['valid' => false, 'message' => 'Minimum order amount for this code is $' . number_format($promo['min_order_amount'], 2)];
        }

        // Calculate discount
        $discount = 0;
        if ($promo['discount_type'] === 'percent') {
            $discount = ($promo['discount_value'] / 100) * $orderAmount;
            if ($promo['max_discount'] !== null && $discount > $promo['max_discount']) {
                $discount = $promo['max_discount'];
            }
        } else {
            $discount = $promo['discount_value'];
        }

        // Ensure discount doesn't exceed order amount
        if ($discount > $orderAmount) {
            $discount = $orderAmount;
        }

        return [
            'valid' => true,
            'promo_id' => $promo['id'],
            'code' => $promo['promo_code'],
            'discount' => $discount,
            'message' => 'Code applied successfully!'
        ];
    }

    public function incrementUsage($id)
    {
        $stmt = $this->pdo->prepare("UPDATE promotions SET used_count = used_count + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
